<?php

namespace App\Traits;

trait generalModel
{
    use helper;
    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
    }
}