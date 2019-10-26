<?php

namespace App\Models\v2;

use App\Models\v2\Season;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Relic extends Eloquent
{
    protected $fillable = ['season_id', 'name', 'desc', 'active', 'active_icon', 'inactive_icon', 'complexity', 'clues'];
    
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
