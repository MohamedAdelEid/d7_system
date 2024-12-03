<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'payment_transactions';
    public $guarded = [];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function Payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
    public function Transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
