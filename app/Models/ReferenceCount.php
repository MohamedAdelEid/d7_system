<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceCount extends Model
{
    use HasFactory;
    protected $table = 'reference_counts';

    public $guarded = [];
}
