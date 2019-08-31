<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\Purchase;
use App\Repositories\User\UserRepository;

class FivePlusSkeletonPurchase implements Purchase
{

	protected $plan, $user;

	public function __construct($plan, $user)
	{
		$this->plan = $plan;
		$this->user = $user;
	}

    public function create($planData)
    {

    	/** Add purchase in the database **/
    	$planPurchase 				  = new PlanPurchase();
		$planPurchase->user_id 		  = $this->user->id;
		$planPurchase->plan_id 		  = $planData->plan_id;
		$planPurchase->country_code   = $planData->country_code;
		$planPurchase->skeleton_keys_amount     = (int)$this->plan->skeleton_keys_amount;
		// $planPurchase->expandable_skeleton_keys = (int)$this->plan->skeleton_keys_amount;
		$planPurchase->price 		  = (float)$this->plan->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();
    	
    	(new UserRepository($this->user))->addSkeletonsBucket($this->plan->skeleton_keys_amount);
    	
    	/** return the available skeleton keys **/
    	return ['skeletons_bucket_size'=> $this->user->skeletons_bucket];
    }
}