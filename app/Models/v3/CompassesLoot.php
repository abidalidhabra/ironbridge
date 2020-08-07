<?php

namespace App\Models\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CompassesLoot extends Eloquent
{
    protected $fillable = ['min', 'max', 'compasses'];

}
