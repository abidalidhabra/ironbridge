<?php

namespace App\Models\v1;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Jenssegers\Mongodb\Auth\User as Authenticatable;
// use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Maklad\Permission\Traits\HasRoles;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Notifiable,Authenticatable, Authorizable, HasRoles;

    protected $guard_name = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['_id','email','name','password','remember_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function passwordSetLink()
    {
        return $this->hasOne('App\Models\v1\AdminPasswordSetLink');
    }
}
