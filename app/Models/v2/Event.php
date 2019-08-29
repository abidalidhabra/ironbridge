<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Event extends Eloquent
{
    
    /** status ->  [tobestart, running, closed, finished, terminated] **/
	protected $fillable = [
        'name',
        'type',
        'coin_type',
        'rejection_ratio',
        'winning_ratio',
        'city_id',
        'user_id',
        'fees',
        'starts_at',
        'ends_at',
        'event_days',
        'discount_details',
        'hunt_id',
        'map_reveal_date',
        'hunt_clues',
        'coin_number',
        'discount_till',
        'discount',
        'description',
        'attempts',
        'status',
        'participation',
        'map_delay_time'
    ];    

    protected $dates = [
        'starts_at',
        'ends_at',
        'map_reveal_date',
        'discount_till'
    ];

     protected $attributes = [
        'status' => 'tobestart',
    ];

    protected $appends = [
        'discount_amount',
        'discount_countdown',
        'play_countdown',
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\v1\City','city_id');
    }

    public function prizes()
    {
        return $this->hasMany('App\Models\v2\EventsPrize','event_id');
    }

    public function event_map_time_delay()
    {
        return $this->hasMany('App\Models\v2\EventMapTimeDelay','event_id');
    }



    public function participations()
    {
        return $this->hasMany('App\Models\v2\EventsUser');
    }

    public function getDiscountAmountAttribute()
    {
        return round($this->fees - ($this->fees * ($this->discount / 100)), 2);
    }

    public function getDiscountCountDownAttribute()
    {
        return ($this->discount_till > now())? $this->discount_till->diffInSeconds(): 0;
    }

    public function getPlayCountDownAttribute()
    {
        return ($this->starts_at > now())? $this->starts_at->diffInSeconds(): 0;
    }

     /**
     * Scope a query to only include upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        $query->where('ends_at', '>=', now());
    }

    /**
     * Scope a query to return events having requested city.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHavingCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope a query to return paticipation of user only if he/she is participated in it.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithParticipation($query, $userId)
    {
        return $query->with(['participations'=> function($query) use ($userId){
            $query->where('user_id', $userId)->select('_id', 'event_id', 'user_id', 'status');
        }]);
    }

    /**
     * Scope a query to return First Rank Prize.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithWinningPrize($query)
    {
        return $query->with(['prizes'=> function($query) {
            $query->where(function($query){
                $query->orWhere('rank', 1)->orWhere('start_rank', 1);
            })
            ->select('_id','event_id','group_type','prize_type','prize_value','rank', 'start_rank', 'end_rank');
        }]);
    }

    /**
     * Scope a query to only include upcoming events and participated events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSoonActivatedOrParticipated($query, $userId)
    {
        return $query->whereHas('participations', function($query) use ($userId){
                    $query->where('user_id', $userId);
                })
                ->orWhere('starts_at', '>=', now())
                ->orWhere('event_days.0.from', '>=', now());
    }
}

    