<?php

namespace App\Repositories\Hunt;

use App\Repositories\User\UserRepository;

class MinigameNodeClaimPrizeService
{

    private $user;
    private $userRepository;

    public function setUser($user)
    {
        $this->user = $user;
        $this->userRepository = (new UserRepository($this->user));
        return $this;
    }

    public function do()
    {
        if (rand(0, 1)) {
            return ['gold_balance'=> $this->userRepository->addSkeletonKeys(1)];
        }else {
            return ['available_skeleton_keys'=> $this->userRepository->addGold(15)];
        }
    }
}