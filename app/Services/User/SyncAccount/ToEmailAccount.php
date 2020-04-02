<?php

namespace App\Services\User\SyncAccount;

use App\Http\Controllers\Admin\UserController;
use App\Repositories\User\UserRepository;
use App\Services\Traits\UserTraits;

class ToEmailAccount
{
	
	use UserTraits;

	protected $targetUser;
	protected $userRepository;

	public function __construct()
	{
		$this->userRepository = (new UserRepository)->getModel();
	}

	public function targetUser($targetUserId)
	{
		$this->targetUser = $this->userRepository->find($targetUserId);
		return $this;
	}

	public function migrate($targetUserId)
    {

    	/**
    	* 1. Remove all the existing data
    	* 2. merge the data
    	**/

    	// HuntUser::where('user_id', $targetUserId)->get()->map(function($huntUser){
     //        $huntUser->hunt_user_details()->delete();
     //        $huntUser->delete();
     //    });
    	// $this->targetUser->practice_games()->delete();
    	// $this->targetUser->plans_purchases()->delete();
    	// $this->targetUser->events()->delete();
    	// $this->targetUser->user_relic_map_pieces()->delete();
    	// $this->targetUser->chests()->delete();
    	// $this->targetUser->answers()->delete();
    	// $this->targetUser->assets()->delete();
    	// $this->targetUser->reported_locations()->delete();

    	$this->targetUser->fill(
    		'nodes_status'=> $this->user->nodes_status,
    		'registration_completed'=> $this->user->registration_completed,
    		'skeleton_keys'=> $this->user->skeleton_keys,
    		'gold_balance'=> $this->user->gold_balance,
    		'skeletons_bucket'=> $this->user->skeletons_bucket,
    		'pieces_collected'=> $this->user->pieces_collected,
    		'widgets'=> $this->user->widgets,
    		'first_login'=> $this->user->first_login,
    		'tutorials'=> $this->user->tutorials,
    		'agent_status'=> $this->user->agent_status,
    		'relics'=> $this->user->relics,
    		'power_status'=> $this->user->power_status,
    		'ar_mode'=> $this->user->ar_mode,
    		'avatar'=> $this->user->avatar,
    		'nodes_status'=> $this->user->nodes_status,
    		'buckets'=> $this->user->buckets,
    		'streaming_relic_id'=> $this->user->streaming_relic_id,
    		'mgc_status'=> $this->user->mgc_status,
    		'minigame_tutorials'=> $this->user->minigame_tutorials
    	);
    	dd($this->targetUser);
        $this->targetUser->save();

        return response()->json(['message'=> 'Account has been successfully reset.']);
    }
}