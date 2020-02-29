<?php

namespace App\Models\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AssetsLog extends Eloquent
{
    protected $fillable = ['user_id', 'type', 'compasses', 'from'];

    public function scopeCompasses($query)
    {
    	return $query->where('type', 'compass');
    }
}
