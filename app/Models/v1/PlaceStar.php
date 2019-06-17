<?php

namespace App\Models\v1;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PlaceStar extends Eloquent
{
    protected $fillable = [
        'place_id',
		'complexity',
    ];

    public function place()
    {
    	return $this->belongsTo('App\Models\v1\TreasureLocation','place_id');
    }

    public function place_clues()
    {
        return $this->hasOne('App\Models\v1\PlaceClue','place_star_id');
    }

}
