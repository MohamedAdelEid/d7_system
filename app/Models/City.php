<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';
    public $guarded = [];



    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function branches(){
        return $this->belongsToMany(Branch::class, 'city_branch', 'city_id', 'branch_id');
    }
}
