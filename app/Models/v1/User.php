<?php

namespace App\Models\v1;

use App\Models\v2\AgentComplementary;
use App\Models\v2\Relic;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
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
        'pieces_collected',
        'minigame_tutorials',
        'first_login',
        'additional',
        'tutorials',
        'agent_status',
        'relics',
        'power',
        'ar_mode',
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
        'power_status.full_peaked_at',
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
        'pieces_collected' => 0,
        'widgets' => [],
        'first_login' => true,
        'tutorials'=> [
            'avatar'=> null,
            'store'=> null,
            'profile'=> null,
            'home'=> null,
            'minigames'=> null,
        ],
        'agent_status'=> [
            'xp'=> 500,
            'level'=> 1
        ],
        'relics'=> [],
        'power_status'=> [
            'power'=> 0
        ],
        'ar_mode'=> true,
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

    public function getMinigameTutorialsAttribute($miniGames)
    {
        return collect($miniGames)->map(function($miniGame){
            $miniGame['completed_at'] = ($miniGame['completed_at'])? $miniGame['completed_at']->toDateTime()->format('Y-m-d H:i:s'): null;
            return $miniGame;
        });
    }

    public function getTutorialsAttribute($miniGames)
    {
        return collect($miniGames)->map(function($ts){
            return ($ts)? $ts->toDateTime()->format('Y-m-d H:i:s'): null;
        });
    }

    public function getFreeOutfitTakenAttribute()
    {
        if ($this->first_login == true) {
            return false;
        }else{
            $userWidgets = collect($this->widgets)->pluck('id');
            $freeOutfeets = WidgetItem::where('free', true)->get()->pluck('_id');
            $contains = false;
            foreach ($freeOutfeets as $outfit) {
                if ($userWidgets->contains($outfit)) {
                    $contains = true;
                    break;
                }
            }
            return $contains;
        }
    }

    public function getAvailableComplexities()
    {
        return AgentComplementary::where('agent_level', '<=', $this->agent_status['level'])->get()->pluck('complexity')->filter()->values();
    }

    public function getRelicsAttribute($value)
    {
        return collect($value);
    }

    public function relics_info()
    {
        return $this->belongsToMany(Relic::class, null, 'users', 'relics._id');
    }

    public function getPowerStatusAttribute($value)
    {
        if (isset($value['full_peaked_at'])) {
            $value['full_peaked_at'] = $value['full_peaked_at']->toDateTime()->format('Y-m-d H:i:s');
        }
        return $value;
    }
}
