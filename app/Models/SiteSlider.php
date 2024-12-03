<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class SiteSlider extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'site_sliders';

    public $guarded = [];
}
