<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\Purchase;
use App\Repositories\User\UserRepository;

class SkeletonPurchase implements Purchase
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
		$planPurchase->skeleton_keys  = (int)$planData->skeleton_keys;
		$planPurchase->price 		  = (float)$this->plan->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();

    	/** Add skeleton keys in user's table **/
    	$availableSkeletonKeys = (new UserRepository($this->user))->addSkeletonKeys($this->plan->skeleton_keys);
    	
        /** Deduct User Gold **/
        $goldBalance = (new UserRepository($this->user))->deductGold($this->plan->gold_price);

    	/** return the available skeleton keys **/
    	return ['available_skeleton_keys'=> $availableSkeletonKeys, 'available_gold_balance'=> $goldBalance];
    }
}