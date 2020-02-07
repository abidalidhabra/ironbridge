<?php

namespace App\Services\Hunt;

use App\Repositories\XPManagementRepository;
use App\Services\User\AddXPService;
use stdClass;

class XPDistributionService
{
	
	public $user;
	public $huntUser;
	public $xPManagementRepository;
	public $addXPService;

	public function __construct()
	{
        $this->xPManagementRepository = new XPManagementRepository;
	}
	/**
     * @param mixed $huntUser
     *
     * @return self
     */
    public function setHuntUser($huntUser)
    {
        $this->huntUser = $huntUser;

        return $this;
    }

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

	public function add($treasureCompleted)
    {
        /**
            -> Add XP twice IF:
                -> relic field is not null.
                -> all map pieces have collected.
        **/

        $xpReward = [];
        if($this->user->tutorials['home']){

        	$this->addXPService = (new AddXPService)->setUser($this->user);
            
            if ($this->huntUser->relic_id) {
                $xp = $this->addXPForRelic($treasureCompleted);
            }else{
                $xp = $this->addXPForRandomHunt();
            }

            $xpReward = $this->addXPService->add($xp);
        }
        return (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass;
    }

    public function addXPForRelic($treasureCompleted)
    {
        $relic = $this->huntUser->relic;
        $xp = $relic->completion_xp['clue'];
        if ($treasureCompleted) {
            $xp += $relic->completion_xp['treasure'];
        }
        return $xp;
    }

    public function addXPForRandomHunt()
    {
        $complexity = $this->huntUser->complexity;
        $xp = $this->xPManagementRepository->getModel()->where(['event'=> 'clue_completion', 'complexity'=> $complexity])->first()->xp;
        // if ($treasureCompleted) {
        //     $xp += $this->xPManagementRepository->getModel()->where(['event'=> 'treasure_completion', 'complexity'=> $complexity])->first()->xp;
        // }
        return $xp;
    }
}