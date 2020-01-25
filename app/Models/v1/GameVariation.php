<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Storage;

class GameVariation extends Eloquent
{

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "variation_name", 
        "variation_size", 
        "variation_complexity",
        "variation_image",
        "sudoku_id", 
        "row", 
        "column",
        'target',
        "number_generate", 
        "reveal_number", 
        "game_id",
        'no_of_balls',
        'bubble_level_id'
    ];
    
    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game');
    }

    public function getVariationImageAttribute($value)
    {
        if (!empty($value)) {
            $images = [];
            foreach ($value as $key => $image) {
                if (Storage::disk('public')->has('game_variations/'.$image) && !is_null($image)) {
                    $images[$key] = asset('storage/game_variations/').'/'.$image;
                }
            }
            return $images;
        } else {
            return [];
        }
    }
}
