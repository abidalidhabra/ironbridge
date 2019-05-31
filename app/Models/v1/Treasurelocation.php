<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Treasurelocation extends Eloquent
{

	protected $table = 'new_city_info';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['latitude','longitude','place_name','place_id','boundary_arr','boundingbox','city','province'];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'boundary_arr' => 'array',
        'boundingbox' => 'array',
    ];
    
    public function getLatitudeAttribute($value){
        return (float)$value;
    }

    public function getLongitudeAttribute($value){
        return (float)$value;
    }

    public function getBoundingboxAttribute($value){
        $val = trim(html_entity_decode(preg_replace('/\s\s+/', ' ', $value)));
        $val = preg_replace('/\"/', '', $val);
        $val = str_replace(" ","",$val);
        $val = str_replace('[',"",$val);
        $val = str_replace(']',"",$val);
        $val = explode(',',$val);
        return array_filter($val);
        //return stripslashes(html_entity_decode($value));
	}
}
