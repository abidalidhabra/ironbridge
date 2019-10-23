<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SeasonalHunt extends Eloquent
{
    protected $fillable = ['name', 'complexity', 'clues'];

}
