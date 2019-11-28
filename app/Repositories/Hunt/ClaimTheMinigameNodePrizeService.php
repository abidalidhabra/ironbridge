<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntStatistic;
use App\Repositories\User\UserRepository;
use App\Repositories\XPManagementRepository;
use App\Services\User\AddXPService;
use Illuminate\Support\Facades\DB;
use stdClass;

class ClaimTheMinigameNodePrizeService
{

    private $user;
    private $xPManagementRepository;

    public function __construct()
    {
        $this->xPManagementRepository = (new XPManagementRepository)->where(['event'=> 'clue_completion', 'complexity'=> 1])->first();
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function do()
    {
        // add the xp in users account
        $xpReward = (new AddXPService)->setUser($this->user)->add(($this->xPManagementRepository->xp * 2));
        $rewardData['xp_reward'] = (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass;
        $rewardData['agent_status'] = $this->user->agent_status;
        // get the agent stack
        $rewardData['agent_stack'] = (new UserRepository($this->user))->getAgentStack();
        return $rewardData;
    }
}