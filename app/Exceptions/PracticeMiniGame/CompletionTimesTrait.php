<?php

namespace App\Exceptions\PracticeMiniGame;

use stdClass;

trait CompletionTimesTrait {

	private $completionTimes = 0;
    private $availableSkeletonKeys = 0;
    private $lastPlay;
    private $XPReward;

    public function __construct($message) 
    {
        parent::__construct($message);
    }

    public function setCompletionTimes($completionTimes)
    {
        $this->completionTimes = $completionTimes;
        return $this;
    }

    public function setAvailableSkeletonKeys($availableSkeletonKeys)
    {
        $this->availableSkeletonKeys = $availableSkeletonKeys;
        return $this;
    }

    public function setLastPlay($lastPlay)
    {
        $this->lastPlay = $lastPlay;
        return $this;
    }

    public function setXPReward($XPReward)
    {
        $this->XPReward = $XPReward;
        return $this;
    }

	public function getcompletionTimes()
    {
        return $this->completionTimes;
    }

    public function getavailableSkeletonKeys()
    {
        return $this->availableSkeletonKeys;
    }

    public function getLastPlay()
    {
        return $this->lastPlay;
    }

    public function getXPReward()
    {
        return $this->XPReward;
    }
}