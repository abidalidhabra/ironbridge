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
        'first_name', 'last_name', 'email', 'password', 'username', 'dob', 'gender', 'referral_id', 'reffered_by', 'firebase_ids', 'otp', 'location', 'address','registration_completed', 'gold_balance','settings','device_type'
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
        'gold_balance'   => 0,
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

    // /**
    //  *
    //  * Get the avatar selected by the user
    //  * @var object
    //  */
    public function avatar()
    {
        return $this->hasOne('App\Models\v1\UserAvatar');
    }

    /**
     *
     * Get the events participated by the user
     * @var array
     */
    public function event_participations()
    {
        return $this->hasMany('App\Models\v1\EventParticipation');
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
        return $this->hasOne('App\Models\v1\HuntUser');
    }
}
