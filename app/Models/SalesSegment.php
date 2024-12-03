<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesSegment extends Model
{
    use HasFactory;
    protected $table = 'sales_segments';
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'sales_segment_id', 'id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_segment_products', 'sales_segment_id', 'product_id');
    }
    public function pivot()
    {
        return $this->belongsToMany(Product::class, 'sales_segment_products', 'sales_segment_id', 'product_id','unit_id')->withPivot('price', 'unit_id');
    }
}
