<?php

namespace App\Services\HuntReward;

use App\Repositories\HuntRewardDistributionHistoryRepository;

class RecordTheGold
{
    public function add($rewardData)
    {
        return (new HuntRewardDistributionHistoryRepository)->create($rewardData);
    }
}