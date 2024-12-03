<?php

namespace App\Models;

use App\Traits\helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Category extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory, SoftDeletes, helper;
    use HasRecursiveRelationships;
    protected $table = 'categories';
    public $translatedAttributes = ['name'];
    protected $guarded = [];

    public function products()  {
        return $this->hasMany(Product::class);
    }
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function getParentKeyName()
    {
        return 'parent_id';
    }

    public function getLocalKeyName()
    {
        return 'id';
    }

    public function scopeMain($query){
        $query->where('parent_id', null);
    }

    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
    }

    public function getPath(){
        $cats = $this->ancestors()->orderBy('depth', 'ASC')->get();
        $path = '';
        foreach($cats as $cat){
            $path .= '/' . $cat->name;
        }

        return $path;
    }
}
