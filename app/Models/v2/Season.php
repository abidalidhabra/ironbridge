<?php

namespace App\Models\v2;

use App\Models\v2\Relic;
use App\Models\v2\SeasonalHunt;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Season extends Eloquent
{
    protected $fillable = ['name', 'slug', 'active', 'active_icon', 'inactive_icon'];

    public function path()
    {
        return route('admin.seasons.show', $this->slug);
    }

    public function hunts()
    {
        return $this->hasMany(SeasonalHunt::class);
    }

    public function relics()
    {
        return $this->hasMany(Relic::class);
    }
}
