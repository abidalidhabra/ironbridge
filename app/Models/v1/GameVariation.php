<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

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
        "updated_at", 
        "sudoku_id", 
        "row", 
        "column", 
        "number_generate", 
        "reveal_number", 
        "game_id"
    ];
    
    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game');
    }
}
