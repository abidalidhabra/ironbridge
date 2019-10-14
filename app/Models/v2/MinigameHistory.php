<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MinigameHistory extends Eloquent
{
    protected $fillable = ['game_id', 'time', 'score', 'action', 'from', 'random_mode', 'complexity'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'time' => 'integer',
        'score' => 'integer',
        'random_mode' => 'boolean',
        'complexity' => 'integer',
    ];
}
