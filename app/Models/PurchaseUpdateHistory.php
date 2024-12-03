<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseUpdateHistory extends Model
{
    protected $fillable = [
        'transaction_id',
        'old_total',
        'new_total',
        'old_final_price',
        'new_final_price',
        'changes_summary',
        'updated_by'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}