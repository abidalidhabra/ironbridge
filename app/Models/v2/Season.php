<?php

namespace App\Models\v2;

use App\Models\v2\SeasonalHunt;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Season extends Eloquent
{
    protected $fillable = ['name', 'active'];

    public function hunts()
    {
        return $this->hasMany(SeasonalHunt::class);
    }
}
