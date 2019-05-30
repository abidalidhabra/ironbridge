<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Game extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'variation_name', 
        'variation_complexity', 
        'row', 
        'column', 
        'bubble_level_id', 		/* This field must have value from 1 to 5 */
        'sudoku_id', 			/* This field must have value from 1 to 15 */
        'word_search',			/* This Field must have array of words */
        'target', 
        'game_id', 
        'no_of_balls', 
        'variation_image', 
        'variation_size', 
        'number_generate', 
        'reveal_number', 
    ];


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
