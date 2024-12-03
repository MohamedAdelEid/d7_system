<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'branchs';
    protected $guarded = [];

    public function scopeActive($query){
        $query;
    }

    public function Users(){
        return $this->belongsToMany(User::class, 'user_branch', 'branch_id', 'user_id');
    }

    public function usersMain(){
        return $this->hasMany(User::class);
    }

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function Accounts(){
        return $this->belongsToMany(Account::class, 'account_branch', 'branch_id', 'account_id');
    }

    public function CashAccount(){
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function CreditAccount(){
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    public function cities(){
        return $this->belongsToMany(City::class, 'city_branch', 'branch_id', 'city_id');
    }

    public function scopeFoMe($query){
        if(!auth()->user()->super){
            $query->whereHas('Users', function($query){
                $query->where('user_id', auth()->user()->id);
            });
        }

        return $query;
    }
    protected static function booted(): void
    {
        static::addGlobalScope('getBranchesByUserAuth', function (Builder $builder) {

            $user_id = \Auth::user()->id;
            
            $branches_ids = \DB::table('user_branch')->where('user_id',$user_id)->pluck('branch_id');

            if (!auth()->user()->super) {
                $builder->whereIn('branchs.id',$branches_ids);
            }
           
        });
    }
    public function ProductBranch(){
        return $this->hasMany(ProductBranch::class, 'branch_id');
    }
    public function governorate(){
        return $this->belongsTo(Governorate::class);
    }
}
