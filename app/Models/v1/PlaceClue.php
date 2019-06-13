<?php

namespace App\Models\v1;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PlaceClue extends Eloquent
{
    protected $fillable = [
        'place_star_id',
		'coordinates',
        'game_id',
        'game_variation_id'
    ];

    protected $casts = [
        'coordinates' => 'array',
    ];

    public function place_star()
    {
    	return $this->belongsTo('App\Models\v1\PlaceStar');
    }

}
