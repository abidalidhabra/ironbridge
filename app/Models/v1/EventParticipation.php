<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventParticipation extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'user_id', 'completed_levels'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'completed_levels' => 'array',
    // ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'completed_levels' => [],
    ];

    public function history()
    {
        return $this->hasMany('App\Models\v1\ParticipantHistory');
    }
}
