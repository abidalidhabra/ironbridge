<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ComplexityTarget extends Eloquent
{
    protected $fillable = [
        'game_id', 'complexity', 'target'
    ];
}
