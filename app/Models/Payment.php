<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;
    protected $table = 'payments';
    public $guarded = [];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function PaymentTransaction()
    {
        return $this->hasMany(PaymentTransaction::class,'payment_id');
    }
}
