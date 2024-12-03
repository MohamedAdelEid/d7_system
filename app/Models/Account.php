<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\AccountNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory,Notifiable;
    protected $table = 'accounts';
    public $guarded = [];
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('getAccountsByByranchUserAuth', function (Builder $builder) {

            $cashAccountidsForAuthUser = Auth::user()->Branches->pluck('cash_account_id');
            $creditAccountidsForAuthUser = Auth::user()->Branches->pluck('credit_account_id');

            if (!auth()->user()->super) {

                $builder->whereIn('id',$cashAccountidsForAuthUser)
                ->whereIn('id',$creditAccountidsForAuthUser);
            }
           
        });
    }
   
}
