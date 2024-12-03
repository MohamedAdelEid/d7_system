<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;
    protected $table = 'governorates';
    public $guarded = [];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
