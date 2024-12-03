<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    public $guarded = [];

    public function getImageInvoiceAttribute($value)
    {
        // If you want to return the full URL for an image stored in storage/app/public
        return $value ? $value : asset('default.png');
    }
    
}
