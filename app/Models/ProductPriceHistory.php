<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    use HasFactory;
    protected $table = 'product_price_histories';
    protected $fillable = ['product_id','old_unit_price','new_unit_price','changed_by','unit_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function changedBy()
    {
        return $this->belongsTo(User::class,'changed_by');
    }
    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
