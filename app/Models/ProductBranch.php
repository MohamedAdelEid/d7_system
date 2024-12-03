<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBranch extends Model
{
    use HasFactory;
    protected $table = 'product_branch';
    protected $fillable = ['product_id', 'branch_id'];
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
