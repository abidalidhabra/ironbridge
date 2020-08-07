<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntStatistic;
use App\Repositories\User\UserRepository;

class ClaimTheSkeletonNodePrizeService
{

    private $user;
    private $userRepository;
    private $huntStatistic;

    public function __construct()
    {
        $this->huntStatistic = HuntStatistic::first(['_id', 'skeleton_keys_for_node']);
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        $this->userRepository = (new UserRepository($this->user));
        return $this;
    }

    public function do()
    {
        return [
            'skeleton_keys'=> [
                'balance'=> $this->userRepository->addSkeletonKeys($this->huntStatistic->skeleton_keys_for_node),
                'credited'=> $this->huntStatistic->skeleton_keys_for_node
            ]
        ];
    }
}