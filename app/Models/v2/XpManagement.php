<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class XpManagement extends Eloquent
{
    protected $fillable = ['event','name','complexity', 'xp'];
}
