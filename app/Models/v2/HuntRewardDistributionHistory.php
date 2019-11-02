<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntRewardDistributionHistory extends Eloquent
{
    protected $fillable = ['hunt_user_id', 'user_id', 'type', 'golds', 'relic_id'];
}
