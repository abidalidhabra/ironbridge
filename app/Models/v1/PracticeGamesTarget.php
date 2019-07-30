<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Storage;

class PracticeGamesTarget extends Eloquent
{
    protected $fillable = [
        'game_id', 'target','variation_images'/*,'number_generate','variation_size'*/
    ];

    public function game()
    {
        return $this->belongsTo('App\Models\v1\Game','game_id');
    }


    public function getVariationImagesAttribute($value)
    {
        if (!empty($value)) {
            $images = [];
            foreach ($value as $key => $image) {
                if (Storage::disk('public')->has('practice_games_assets/'.$image) && !is_null($image)) {
                    $images[] = asset('storage/practice_games_assets/').'/'.$image;
                }
            }
            $image = collect($images)->shuffle()->first();
            return $image;
            // return $images;
        } else {
            // return [];
            return "";
        }
    }
}
