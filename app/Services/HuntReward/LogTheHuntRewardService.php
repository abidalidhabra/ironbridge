<?php

namespace App\Services\HuntReward;

use App\Repositories\HuntRewardDistributionHistoryRepository;

class LogTheHuntRewardService
{
    public function add($rewardData)
    {
        (new HuntRewardDistributionHistoryRepository)->create($rewardData);
    }
}