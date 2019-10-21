<?php

namespace App\Services\HuntReward\Factory;

use App\Services\HuntReward\RecordTheGold;
use Exception;

class RecordServiceFactory
{
    public function init($type)
    {
        if ($type == 'gold') {
            return new RecordTheGold;
        }

        throw new Exception("Invalid reward type provided");
    }
}