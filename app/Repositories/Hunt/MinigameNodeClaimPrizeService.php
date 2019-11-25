<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntStatistic;
use App\Repositories\User\UserRepository;

class MinigameNodeClaimPrizeService
{

    private $user;
    private $userRepository;
    private $huntStatistic;

    public function FunctionName($value='')
    {
        $this->huntStatistic = HuntStatistic::first(['_id', 'gold', 'skeleton_keys']);

    }
    public function setUser($user)
    {
        $this->user = $user;
        $this->userRepository = (new UserRepository($this->user));
        return $this;
    }

    public function do()
    {
        if (rand(0, 1)) {
            return ['gold_balance'=> $this->userRepository->addSkeletonKeys($this->huntStatistic->gold)];
        }else {
            return ['available_skeleton_keys'=> $this->userRepository->addGold($this->huntStatistic->skeleton_keys)];
        }
    }
}