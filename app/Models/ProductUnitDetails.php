<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnitDetails extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'product_unit_details';
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function GetAllUnits(){
        return Unit::whereIn('id', $this->product->sub_unit_ids)->get();
    }
    
}
