<?php

namespace App\Models\v3;

use App\Models\v3\Country;
use App\Models\v3\Event;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Timezone extends Eloquent
{
    protected $fillable = ['timezone'];

}
