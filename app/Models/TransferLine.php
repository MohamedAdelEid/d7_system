<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferLine extends Model
{
    use HasFactory;
    protected $table = 'transfer_lines';
    protected $fillable = ['transaction_id', 'product_id', 'quantity','unit_id','main_unit_quantity'];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function Unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
