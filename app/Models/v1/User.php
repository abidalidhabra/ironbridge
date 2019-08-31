<?php

namespace App\Models\v1;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'username',
        'dob',
        'gender',
        'referral_id',
        'reffered_by',
        'firebase_ids',
        'otp',
        'location',
        'address',
        'registration_completed',
        'gold_balance',
        'settings',
        'device_type',
        'widgets',
        'avatar',
        'skeleton_keys',
        'skeletons_bucket',
        // 'expnadable_skeleton_keys',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'dob',
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'registration_completed' => false,
        'gender' => null,
        'settings'   => [
            'sound_fx' => true,
            'music_fx' => true,
        ],
        'skeleton_keys' => [],
        'gold_balance'   => 0,
        'skeletons_bucket' => 5,
        // 'expnadable_skeleton_keys'   => 0,
        // 'user_widgets' => [],
        // 'used_widgets' => [],
    ];

    protected $appends = [
        'available_skeleton_keys'
    ];
    
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
        return [];
    }

    /**
     *
     * Get the events participated by the user
     * @var array
     */
    public function events()
    {
        return $this->hasMany('App\Models\v2\EventsUser');
    }

    public function balance_sheet()
    {
        return $this->hasMany('App\Models\v1\UserBalancesheet');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\v1\UserTransaction');
    }

    public function hunt_user()
    {
        return $this->hasMany('App\Models\v1\HuntUser');
    }

    public function hunt_user_v1()
    {
        return $this->hasMany('App\Models\v2\HuntUser');
    }

    public function avatar_detail()
    {
        return $this->belongsTo('App\Models\v1\Avatar', 'avatar.avatar_id', '_id');
    }

    public function practice_games()
    {
        return $this->hasMany('App\Models\v2\PracticeGameUser');
    }
    
    public function getAvailableSkeletonKeysAttribute()
    {
        return ($this->skeleton_keys)? collect($this->skeleton_keys)->where('used_at', null)->count():0;
    }

    public function plans_purchases()
    {
        return $this->hasMany('App\Models\v2\PlanPurchase');
    }
}
