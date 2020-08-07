<?php

namespace App\Models\v1;

use App\Models\v2\AgentComplementary;
use App\Models\v2\MinigameHistory;
use App\Models\v2\Relic;
use App\Models\v2\UserRelicMapPiece;
use App\Models\v3\AssetsLog;
use App\Models\v3\ChestUser;
use App\Models\v3\City;
use App\Models\v3\EventUser;
use App\Models\v3\UserQA;
use App\ReportedLocation;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use MongoDB\BSON\UTCDateTime;
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
        // 'device_type',
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
        'power_status',
        'ar_mode',
        'last_login_as',
        'facebook_id',
        'google_id',
        'apple_id',
        'apple_data',
        'guest_id',
        'device_info',
        'nodes_status',
        'mgc_status',
        'buckets',
        'hat_selected',
        'compasses',
        'city_id',
        'streaming_relic_id',
        'skipped_relic_id',
        'default_outfit_id',
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

    // protected $dates = [
    //     'dob',
    // ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'first_name'=> '',
        'last_name'=> '',
        'username'=> '',
        'email'=> '',
        'dob'=> '',

        'registration_completed' => false,
        'gender' => 'female',
        'settings'   => [
            'sound_fx' => true,
            'music_fx' => true,
        ],
        'skeleton_keys' => [],
        'gold_balance'   => 500,
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
            'hunt_relic'=> null,
            'hunt_mg_challenge'=> null,
            'hunt_power'=> null,
            'skeleton_key'=> null,
            'hunt_second'=> null,
            'hunt_refresh'=> null,
            'hunt_distance'=> null,
            'relic'=> null,
            'home_second'=> null,
            // 'robo_intro'=> null,
            // 'hunts'=> [
            //     'relic'=> null,
            //     'mg_challenge'=> null,
            //     'power'=> null,
            // ],
        ],
        'agent_status'=> [
            'xp'=> 500,
            'level'=> 1
        ],
        'relics'=> [],
        'power_status'=> [
            'power'=> 97
        ],
        'ar_mode'=> false,
        'avatar'=> [
            "avatar_id" => "5c9b66739846f40e807a4498", 
            "eyes_color" => "#2a5aa1", 
            "hairs_color" => "#e5db96", 
            "skin_color" => "#f0cfb6"
        ],
        'widgets'=> [
            ['id'=> "5d246f230b6d7b1a0a232482", 'selected'=> true],
            ['id'=> "5d246f230b6d7b1a0a23245e", 'selected'=> true],
            ['id'=> "5d246f230b6d7b1a0a23246a", 'selected'=> true],
            ['id'=> "5d246f230b6d7b1a0a232453", 'selected'=> true],
            ['id'=> "5d246f230b6d7b1a0a232476", 'selected'=> true],
            ['id'=> "5d4424455c60e6147cf181b4", 'selected'=> true],
            ['id'=> "5d246f0c0b6d7b19fb5ab590", 'selected'=> true],
            ['id'=> "5d246f0c0b6d7b19fb5ab56d", 'selected'=> true],
            ['id'=> "5d246f0c0b6d7b19fb5ab562", 'selected'=> true],
            ['id'=> "5d246f0c0b6d7b19fb5ab578", 'selected'=> true],
            ['id'=> "5d246f0c0b6d7b19fb5ab584", 'selected'=> true],
            ['id'=> "5d4423d65c60e6147cf181a6", 'selected'=> true],
        ],
        'reffered_by'=> null,
        'additional'=> [],
        'nodes_status'=> [
            'mg_challenge'=> null,
            'power'=> null,
            'bonus'=> null
        ],
        'buckets'=> [
            'chests'=> [
                'capacity'=> 5,
                'collected'=> 0,
                'remaining'=> 5
            ]
        ],
        'hat_selected'=> true,
        'compasses'=> [
            'utilized'=> 0,
            'remaining'=> 0,
        ]
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
        return $this->hasMany(EventUser::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
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
        if ($this->tutorials['home']) {
            return true;
        }else{
            return false;
        }

        // if ($this->tutorials['home'] == true) {
        // if ($this->first_login == true) {
        //     return false;
        // }else{
        //     $userWidgets = collect($this->widgets)->pluck('id');
        //     $freeOutfeets = WidgetItem::where('free', true)->get()->pluck('_id');
        //     $contains = false;
        //     foreach ($freeOutfeets as $outfit) {
        //         if ($userWidgets->contains($outfit)) {
        //             $contains = true;
        //             break;
        //         }
        //     }
        //     return $contains;
        // }
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

    public function setPowerStatusAttribute($value)
    {
        if (isset($value['full_peaked_at']) && is_string($value['full_peaked_at'])) {
            $value['full_peaked_at'] = new UTCDateTime(CarbonImmutable::parse($value['full_peaked_at'])->getTimestamp() * 1000);
        }
        if (isset($value['activated_at']) && is_string($value['activated_at'])) {
            $value['activated_at'] = new UTCDateTime(CarbonImmutable::parse($value['activated_at'])->getTimestamp() * 1000);
        }
        return $this->attributes['power_status'] = $value;
    }

    public function getPowerStatusAttribute($value)
    {
        if (isset($value['full_peaked_at'])) {
            $value['full_peaked_at'] = CarbonImmutable::createFromTimestamp($value['full_peaked_at']->toDateTime()->getTimestamp())->format('Y-m-d H:i:s');
        }
        if (isset($value['activated_at'])) {
            $value['activated_at'] = CarbonImmutable::createFromTimestamp($value['activated_at']->toDateTime()->getTimestamp())->format('Y-m-d H:i:s');
        }
        return $value;
    }

    public function minigames_history()
    {
        return $this->hasMany(MinigameHistory::class);
    }

    public function setRefferedIdAttribute($value)
    {
        return $this->attributes['reffered_id'] = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 8)), 0, 8);
    }
    
    public function setEmailAttribute($value)
    {
        return $this->attributes['email'] = strtolower($value);
    }

    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = Hash::needsRehash($value) ? bcrypt($value): $value;
    }

    public function getDobAttribute($value)
    {
        if (empty($value)) {
            return '';
        }else{
            return CarbonImmutable::createFromTimestamp($value->toDateTime()->getTimestamp())->format('Y-m-d H:i:s');
        }
    }    

    public function getMgcStatusAttribute($miniGames)
    {
        return collect($miniGames)->map(function($miniGame){
            $miniGame['completed_at'] = ($miniGame['completed_at'])? $miniGame['completed_at']->toDateTime()->format('Y-m-d H:i:s'): null;
            return $miniGame;
        });
    }

    public function setDobAttribute($value)
    {
        if (empty($value)) {
            return $this->attributes['dob'] = '';
        }else{
            return $this->attributes['dob'] = new UTCDateTime(CarbonImmutable::parse($value)->getTimestamp() * 1000);
        }
    }

    public function setAppleDataAttribute($value)
    {
        return $this->attributes['apple_data'] = json_decode($value, true);
    }

    public function user_relic_map_pieces()
    {
        return $this->hasMany(UserRelicMapPiece::class);
    }

    public function reported_locations()
    {
        return $this->hasMany(ReportedLocation::class);
    }

    public function assets()
    {
        return $this->hasMany(AssetsLog::class);
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string
     */
    public function routeNotificationForFcm()
    {
        if ($this->device_info['type'] == 'android') {
            return $this->firebase_ids['android_id'];
        }else if($this->device_info['type'] == 'ios') {
            return $this->firebase_ids['ios_id'];
        }
    }

    public function chests()
    {
        return $this->hasMany(ChestUser::class);
    }

    public function answers()
    {
        return $this->hasOne(UserQA::class);
    }
}
