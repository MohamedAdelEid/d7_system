<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBranchDetails extends Model
{
    use HasFactory;
    protected $table = 'product_branch_details';
    public $guarded = [];

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getQuantityByUnit($product, $unit_id, $quantity){
        //if product main unit = unit is
        if($product->unit_id == $unit_id)
            return $quantity;
        
        $unit = Unit::find($unit_id);

        if(!$unit)
            return $quantity;

        if($unit->base_unit_is_largest == 1)
            return $quantity * $unit->base_unit_multiplier;

        if($unit->base_unit_is_largest == 0)
            return $quantity / $unit->base_unit_multiplier;

        return $quantity;
    }
}
