<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class PracticeGamesTarget extends Eloquent
{
    protected $fillable = [
        'game_id', 'target','variation_size'
    ];

    public function game()
    {
        return $this->belongsTo('App\Models\v1\Game','game_id');
    }
}
