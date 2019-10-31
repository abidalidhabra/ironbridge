<?php

namespace App\Models\v2;

use App\Models\v2\Season;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Relic extends Eloquent
{

    protected $fillable = ['season_id', 'name', 'desc', 'active', 'icon', 'complexity', 'clues', 'game_id', 'game_variation_id'];
    
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function getIconAttribute($value)
    {
        return asset('storage/seasons/'.$this->season_id.'/'.$value);
    }
}
