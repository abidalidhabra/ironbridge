<?php

namespace App\Models\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class JokeItem extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image'
    ];

    public function getImageAttribute($value)
    {
    	if (file_exists(public_path('storage/joke_items/'.$value))) {
    		return asset('storage/joke_items/'.$value);
    	}else{
    		return asset('storage/default.png');
    	}
    }
}
