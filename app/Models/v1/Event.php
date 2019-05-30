<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Event extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'city_id', 'starts_at', 'ends_at', 'players', 'entry_fees', 'winning_code', 'event_level', 'sequencial_flow', 'activated_at', 'practice_event'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'  	=> 'datetime',
        'activated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'city_id' => null,
        'sequencial_flow' => false,
        'event_level' => 'compact', /** [compact,substantial]; **/
        'practice_event' => false,
    ];

    public function event_levels()
    {
        return $this->hasMany('App\Models\v1\EventLevel');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\v1\City');
    }

    public function event_participations()
    {
        return $this->hasMany('App\Models\v1\EventParticipation');
    }

    public function event_coins()
    {
        return $this->hasMany('App\Models\v1\EventCoin');
    }

    public function event_winners()
    {
        return $this->hasMany('App\Models\v1\EventWinner');
    }

    public function event_pricemoney()
    {
        return $this->hasOne('App\Models\v1\EventWinner')->where('rank',1);
    }
}
