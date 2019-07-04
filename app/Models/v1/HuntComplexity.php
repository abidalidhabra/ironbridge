<?php

namespace App\Models\v1;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntComplexity extends Eloquent
{

    protected $table = 'hunt_complexities';
    
    protected $fillable = [
        'hunt_id', 
        'complexity',
        'est_completion',
        'distance'
    ];

    protected $attributes = [
        'est_completion' => 0,
        'distance' => 0
    ];

    public function hunt()
    {
    	return $this->belongsTo('App\Models\v1\Hunt','hunt_id');
    }

    public function hunt_clue()
    {
        return $this->hasOne('App\Models\v1\HuntClue','hunt_complexity_id');
    }

    public function hunt_clues()
    {
        return $this->hasMany('App\Models\v1\HuntClue','hunt_complexity_id');
    }

    public function hunt_users()
    {
        return $this->hasMany('App\Models\v1\HuntUser','hunt_complexity_id');
    }
       
}
