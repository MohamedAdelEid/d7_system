<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPurchaseLine extends Model
{
    use HasFactory;
    protected $table = 'transactions_purchase_lines';
    public $guarded = [];

    public function Transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    
    public function Unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
