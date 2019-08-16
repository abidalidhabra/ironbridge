<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Complexitie extends Model
{
    protected $fillable = [
        'name', 'fix_distance'
    ];
}
