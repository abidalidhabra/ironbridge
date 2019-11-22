<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class TreasureNodesTarget extends Eloquent
{
    protected $fillable = [
        'game_id', 
        'score',
    ];
    
    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game');
    }
}
