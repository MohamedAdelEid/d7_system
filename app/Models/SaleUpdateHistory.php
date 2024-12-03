<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleUpdateHistory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'sells_update_histories';
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
