<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['expense_category_id', 'account_id', 'branch_id', 'amount', 'note','created_by'];
    protected $table = 'expenses';

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    
}
