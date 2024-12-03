<?php

namespace App\Traits;

use App\Models\Unit;

trait Stock
{
    public function getQuantityByUnit($product, $unit_id, $quantity){
        //if product main unit = unit is
        if($product->unit_id == $unit_id)
            return $quantity;
        
        $unit = Unit::find($unit_id);

        if(!$unit)
            return $quantity;

        if($unit->base_unit_is_largest == 1)
            return $quantity * $unit->base_unit_multiplier;

        if($unit->base_unit_is_largest == 0 && $unit->base_unit_multiplier != 0)
            return $quantity / $unit->base_unit_multiplier;

        return $quantity;
    }
   
    public function getMainUnitQuantityFromSubUnit($product, $unit_id, $quantity){
        //if product main unit = unit is
        if($product->unit_id == $unit_id) {
            return $quantity;
        }
           
        
        $unit = Unit::find($unit_id);

        if(!$unit)
            return $quantity;

        if($unit->base_unit_is_largest == 1 && $unit->base_unit_multiplier != 0)
            return $quantity / $unit->base_unit_multiplier;

        if($unit->base_unit_is_largest == 0)
            return $quantity * $unit->base_unit_multiplier;

        return $quantity;
    }
}