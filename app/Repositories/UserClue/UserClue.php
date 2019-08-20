<?php

namespace App\Repositories\UserClue;

/**
 * user clue repository
 */
class UserClue
{
	
	private $userClueDetail;
	function __construct($userClueDetail)
	{
		$this->userClueDetail = $userClueDetail;
	}

	public function markClueAsComplete()
	{
		/** Reset the timer if running **/
		$finishedIn = $userClueDetail->finished_in + now()->diffInSeconds($userClueDetail->started_at);
		$userClueDetail->finished_in = $finishedIn;
		$userClueDetail->started_at  = null;
		$userClueDetail->ended_at    = null;
		$userClueDetail->status      = $action;
		$userClueDetail->save();

		/** Check if this was the last clue completed **/
        $stillRemain = $userClueDetail->hunt_user->hunt_user_details()->where('status', '!=', 'completed')->count();

		/** Mark hunt as completed and distribute reward to user **/
        if (!stillRemain) {
            $huntUser = $userClueDetail->hunt_user()->update([ 
        		'status'=>'completed', 
        		'ended_at'=> new MongoDBDate(), 
        		'finished_in'=> $userClueDetail->sum('finished_in') 
        	]);
            return $this->generateReward($huntUserDetail);
        }
	}
}