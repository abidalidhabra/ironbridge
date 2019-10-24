<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AppStatistic extends Eloquent
{
    protected $fillable = ['_id', 'maintenance'];
}
