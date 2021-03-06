<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Storage;

class PracticeGameTarget extends Eloquent
{
    protected $fillable = [
        'game_id', 'targets','variation_images'
    ];

    public function game()
    {
        return $this->belongsTo('App\Models\v1\Game','game_id');
    }

    public function getTargetsAttribute($value)
    {
        return collect($value);
    }

    public function getVariationImagesAttribute($value)
    {
        if (!empty($value)) {
            $images = [];
            foreach ($value as $key => $image) {
                if (Storage::disk('public')->has('practice_games/'.$image) && !is_null($image)) {
                    $images[] = asset('storage/practice_games/').'/'.$image;
                }
            }
            // $image = collect($images)->shuffle()->first();
            // return $image;
            return $images;
        } else {
            return [];
            // return "";
        }
    }
}
