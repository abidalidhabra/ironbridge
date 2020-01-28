<?php

namespace App\Models\v2;

use App\Models\v1\User;
use App\Models\v2\Relic;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserRelicMapPiece extends Eloquent
{
    protected $fillable = ['user_id', 'relic_id', 'piece'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relic()
    {
    	return $this->belongsTo(Relic::class, 'relic_id');
    }
}
