<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Brand extends Model  implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'brands';
    protected $guarded = [];
    public function Products()
    {
        return $this->hasMany(Product::class);
    }
}
