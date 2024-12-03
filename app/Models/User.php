<?php

namespace App\Models;

use App\Traits\helper;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\GetBranchByUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{
    use helper, Notifiable;
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    
    protected $table = 'users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function mainBranch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
    
    public function Branches(){
        return $this->belongsToMany(Branch::class, 'user_branch',  'user_id','branch_id');
    }

    //relations
    public function Image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Activity_logs()
    {
        return $this->hasMany(Activity_log::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function PriceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class);
    }
    
    //
    public function getImage(){
        if($this->Image != null){
            return url('uploads/users/' . $this->Image->src);
        } else {
            return url('uploads/users/default.jpg');
        }
    }

    public function getRole(){
        if(count($this->roles) > 0){
            return $this->roles[0]->name;
        } else {
            return null;
        }
    }

    public function getRoleId(){
        if(count($this->roles) > 0){
            return $this->roles[0]->id;
        } else {
            return null;
        }
    }

    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
    }

    public function has_permission($permission){
        if($this->super == 1)
            return true;
        
        if($this->isAbleTo($permission))
            return true;

        return false;
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'type'       => 'user_api'
        ];
    }
}
