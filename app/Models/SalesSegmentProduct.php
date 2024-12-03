<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesSegmentProduct extends Model
{
    use HasFactory;
    protected $table = 'sales_segment_products';
    protected $fillable = ['sales_segment_id', 'product_id', 'unit_id', 'price'];
    public function SalesSegment(){
        return $this->belongsTo(SalesSegment::class, 'sales_segment_id');
    }
    public function Product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function Unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
