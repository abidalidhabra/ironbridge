<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventWinner extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'rank', 'rank_type', 'price_type', 'price_amount'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'  	=> 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'price_type' => 'INDIVIDUAL', /** [INDIVIDUAL, GROUP] **/
        'price_type' => 'GOLD', /** [GOLD, MONEY] **/
    ];

    public function scopeRank($query,$rank)
    {
        return $query->where('rank',$rank);
    }
}
