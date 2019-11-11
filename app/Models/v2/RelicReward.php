<?php

namespace App\Models\v2;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class RelicReward extends Eloquent
{
    protected $fillable = ['agent_level', 'xps','minigames','complexity','widgets','bucket_size'];
}
