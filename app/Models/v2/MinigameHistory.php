<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use App\Models\v1\Game;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MinigameHistory extends Eloquent
{
    protected $fillable = ['game_id', 'time', 'score', 'action', 'from', 'random_mode', 'complexity', 'hunt_user_detail_id', 'practice_game_user_id', 'user_id'];

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

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
