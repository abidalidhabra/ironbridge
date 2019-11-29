<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntStatistic;
use App\Repositories\User\UserRepository;

class ClaimTheBonusTreasurePrizeService
{

    private $user;
    private $userRepository;
    private $huntStatistic;

    public function __construct()
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
            return [
                'gold'=> [
                    'balance'=> $this->userRepository->addGold($this->huntStatistic->gold),
                    'credited'=> $this->huntStatistic->gold
                ]
            ];
        }else {
            return [
                'skeleton_keys'=> [
                    'balance'=> $this->userRepository->addSkeletonKeys($this->huntStatistic->skeleton_keys),
                    'credited'=> $this->huntStatistic->skeleton_keys
                ]
            ];
        }
    }
}