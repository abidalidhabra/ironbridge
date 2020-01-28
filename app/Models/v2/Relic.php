<?php

namespace App\Models\v2;

use App\Models\v2\HuntRewardDistributionHistory;
use App\Models\v2\HuntUser;
use App\Models\v2\Loot;
use App\Models\v2\Season;
use App\Models\v2\UserRelicMapPiece;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Storage;

class Relic extends Eloquent
{

    protected $fillable = ['name','icon', 'complexity', 'pieces', 'active', 'users','number','loot_tables','completion_xp' /* 'game_id', 'game_variation_id'*/];
    
    public function getIconAttribute($value)
    {
        if (Storage::disk('public')->has('relics/'.$this->complexity.'/'.$value) && !is_null($value)) {
            return asset('storage/relics/'.$this->complexity.'/'.$value);
        } else {
            return '';
        }
    }

    /*public function getPiecesAttribute($value)
    {
        if ($value != "") {
            $data = [];
            foreach ($value as $key => $piece) {
                $data[$key] = $piece;
                if (isset($piece['image']) && $piece['image']!="" && Storage::disk('public')->has('relics/'.$this->complexity.'/'.$piece['image']) && !is_null($piece['image'])) {
                    $data[$key]['image'] = asset('storage/relics/'.$this->complexity.'/'.$piece['image']);
                }
            }
            return $data;
        }
        return $value;
    }*/

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

    public function scopeActive($query)
    {
        $query->where('active', true);
    }

    public function game()
    {
        return $this->belongsTo('App\Models\v1\Game','game_id');
    }

    public function game_variation(){
        return $this->belongsTo('App\Models\v1\GameVariation','game_variation_id');     
    }

    public function hunt_users()
    {
        return $this->hasMany(HuntUser::class);
    }    

    // public function hunt_users_reference()
    // {
    //     return $this->hasMany(HuntUser::class, 'relic_reference_id');
    // }

    public function loot_info()
    {
        return $this->belongsToMany(Loot::class, null, 'relics','loot_tables');
    }

    public function map_pieces()
    {
        return $this->hasMany(UserRelicMapPiece::class);
    }
}
