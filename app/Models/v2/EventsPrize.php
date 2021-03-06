<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class EventsPrize extends Eloquent
{

    protected $fillable = [
        'event_id',
        'group_type',
        'rank',
        'start_rank',
        'end_rank',
        'prize_type',
        'prize_value',
        'map_time_delay'
    ];

    protected $table = 'events_prizes';
}
