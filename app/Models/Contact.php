<?php

namespace App\Models;

use App\Traits\helper;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Contact extends Authenticatable implements JWTSubject
{
    use HasFactory, helper ,Notifiable;
    protected $table = 'contacts';
    
    protected $fillable = [
        'name',
        'phone',
        'password',
        'email',
        'address',
        'type',
        'balance',
        'opening_balance',
        'is_active',
        'activity_type_id',
        'city_id',
        'governorate_id',
        'is_default',
        'credit_limit',
        'sales_segment_id',
        'code',
        'contact_code',
        'contact_type',
        'latitude',
        'longitude',
        'remember_token',
        'verified_at',
    ];
    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];


    public function scopeActive($query){
        $query->where('is_active', 1);
    }

    public function scopeCustomer($query){
        $query->where('type', 'customer');
    }

    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
    }
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    public function salesSegment()
    {
        return $this->belongsTo(SalesSegment::class, 'sales_segment_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'contact_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function getBranch()
    {
        $branch =  DB::table('branchs')->where('governorate_id', $this->governorate_id)->first();

        return  $branch;
        
    
    }
    
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }
    public function generateCode() {
        
        $this->contact_code = rand(1000,9999);
        $this->save();
        
    }
    public function getCreditLimitAttribute($value)
    {
        // Remove trailing zeros if it's a decimal number
        if (strpos($value, '.') !== false) {
            $value = rtrim(rtrim($value, '0'), '.');
        }

        return $value;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getStatistics($date_from = null, $date_to = null){
        $statistics = Transaction::where('contact_id', $this->id)
        ->with(['TransactionSellLines', 'TransactionPurchaseLines', 'PaymentsTransaction'])
        ->when($date_from && $date_to, function ($query) use ($date_from, $date_to) {
            $query->whereBetween('created_at', [$date_from, $date_to]);
        })
        ->get();
        $total_sales = $statistics->sum(function ($transaction) {
            return $transaction->TransactionSellLines->sum('total');
        });
    
        $total_purchases = $statistics->where('type', 'purchase')->where('status', 'final')
        ->where('contact_id', $this->id)->sum('final_price');
    
        $total_payments = $statistics->sum(function ($transaction) {
            return $transaction->PaymentsTransaction->sum('amount');
        });
        $contact = Contact::find($this->id);
        $opening_balance = $contact->opening_balance;
        return [
            'opening_balance' => $opening_balance,
            'total_sales' => $total_sales,
            'total_purchases' => $total_purchases,
            'total_payments' => $total_payments,
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    
}
