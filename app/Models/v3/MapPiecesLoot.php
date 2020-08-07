<?php

namespace App\Models\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MapPiecesLoot extends Eloquent
{
    
    protected $fillable = [
        'min', 'max', 'distribute'
    ];

    public function scopeHope($query)
    {
    	$query->where('distribute', true);
    }

    public function scopeUnhope($query)
    {
    	$query->where('distribute', false);
    }
}
