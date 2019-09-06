<?php

namespace App\Models\v1;
use Storage;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
// use Illuminate\Database\Eloquent\Model;

class News extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'description', 'valid_till','image'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
    	'valid_till'
    ];

    public function getImageAttribute($value)
    {
        if (Storage::disk('public')->has('news/'.$value) && !is_null($value)) {
            return asset('storage/news/').'/'.$value;
        } else {
            return ' ';
        }   
    }
}
