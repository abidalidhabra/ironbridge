<?php

namespace App\Models\v2;

use App\Models\v2\Relic;
use App\Models\v2\SeasonalHunt;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Season extends Eloquent
{
    protected $fillable = ['name', 'slug', 'active', 'icon'];

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

    public function getIconAttribute($value)
    {
        return asset('storage/seasons/'.$this->id.'/'.$value);
    }

    public function scopeActive($query)
    {
        $query->where('active', true);
    }
}
