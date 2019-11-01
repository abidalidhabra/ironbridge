<?php

namespace App\Models\v2;

use App\Models\v2\HuntUser;
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

    public function participations() {
        return $this->hasMany(HuntUser::class, 'hunt_id');
    }

    public function getIconAttribute($value)
    {
        return asset('storage/seasons/'.$this->season_id.'/'.$value);
    }

    public function scopeActive($query)
    {
        $query->where('active', true);
    }
}
