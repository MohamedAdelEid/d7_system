<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $table = 'activity_types';

    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
