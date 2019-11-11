<?php

namespace App\Models\v2;

use App\Models\v2\HuntRewardDistributionHistory;
use App\Models\v2\HuntUser;
use App\Models\v2\Season;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class RelicOld extends Eloquent
{

    protected $fillable = ['season_id', 'name', 'desc', 'active', 'icon', 'complexity', 'clues', 'game_id', 'game_variation_id', 'fees'];
    
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

    public function scopeNotParticipated($query, $userId)
    {
        return $query->whereDoesntHave('participations', function($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function rewards()
    {
        return $this->hasMany(HuntRewardDistributionHistory::class);
    }

    public function path()
    {
        return route('admin.relics.edit', $this->season_id);
    }
}
