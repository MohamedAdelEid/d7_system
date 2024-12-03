<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpoiledLine extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'spoiled_lines';
    protected $fillable = ['transaction_id', 'product_id', 'quantity','unit_id','main_unit_quantity','reason'];
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
