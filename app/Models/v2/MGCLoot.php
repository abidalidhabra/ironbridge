<?php

namespace App\Models\v2;

use App\Models\v2\Relic;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MGCLoot extends Eloquent
{
    protected $table = 'mgc_loots';

    protected $fillable = [
        'complexity', 
        'gold_value',
        'min_range',
        'max_range',
        'widget_value',
        'widgets_order',
        'skeletons',
        'possibility',
        'reward_type',
        'status',
        'relics'
    ];


    public function relics_info()
    {
        return $this->belongsToMany(Relic::class, null, 'mgc_loot_tables', 'relics');
    }
}
