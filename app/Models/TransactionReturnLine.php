<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionReturnLine extends Model
{
    use HasFactory;
    protected $table = 'transactions_return_lines';
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
