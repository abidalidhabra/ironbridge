<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use App\Models\v2\TreasureNodesTarget;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Collections\GameCollection;

class Game extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 
        'name',
        'status',
        'practice_default_active'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function game_variation()
    {
        return $this->hasMany('App\Models\v1\GameVariation','game_id');
    }

    public function complexity_target()
    {
        return $this->hasOne('App\Models\v1\ComplexityTarget','game_id')->select('_id', 'game_id', 'complexity', 'target');
    }

    public function practice_games_targets()
    {
        return $this->hasOne('App\Models\v1\PracticeGameTarget');
    }

    public function treasure_nodes_target()
    {
        return $this->hasOne(TreasureNodesTarget::class);
    }

    public function newCollection(array $models = [])
    {
        return new GameCollection($models);
    }

    // public function practice_mini_games()
    // {
    //     return $this->hasMany('App\Models\v1\PracticeGamesTarget');
    // }
    /**
    
    	Block Puzzle:
    	- target : 1 to Infinite
    	- row and column : 9 * 9, 10 * 10
    
    */

    /**
    
    	2048 Puzzle:
    	- target : [1024, 2048, 4096]
    	- row and column : 4 * 4 TO 8 * 8
    
    */
    

   	/**
    
    	Jigswa Puzzle:
    	- variation_image : Image Name {H:1440 W:2000}
    	- variation_size : [12,35,70,140]
    
    */

    /**

        Bubble Shooter Puzzle:
        - target : 1 to Infinite
        - no_of_balls : 1 to Infinite [Number of balls that user will get at game starting]
        - bubble_level_id : Must be with 1 TO 5

    */

    /**

        Hexa Puzzle | Yatzy Puzzle | Slices Puzzle | Snack Puzzle | Domino Puzzle:
        - target : 1 to Infinite

    */


    /**

        Word Search:
        - word_search : Array containing the words.
        - row and column : *what  will be the size of this ?*

    */

    /**

        Number Puzzle:
        - number_generate : *is there any limit of generating the maximum numbers ?*
        - row and column : *what  will be the size of this ?*

    */

    /**

        Sliding Puzzle:
        - variation_image : Image Name *What will be the image height and width*
        - row and column : *what  will be the size of this ?*

    */

}
