<?php

namespace App\Models\v3;

use App\Models\v1\User;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ChestUser extends Eloquent
{
    protected $fillable = ['user_id', 'place_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
