<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\PlanPurchaseInterface;
use App\Repositories\UserRepository;

class SkeletonPurchase implements PlanPurchaseInterface
{

	protected $plan;

	public function __construct($plan)
	{
		$this->plan = $plan;
	}

    public function create($planData, $user)
    {

    	/** Add purchase in the database **/
    	$planPurchase 				  = new PlanPurchase();
		$planPurchase->user_id 		  = $user->id;
		$planPurchase->plan_id 		  = $planData->plan_id;
		$planPurchase->country_code   = $planData->country_code;
		$planPurchase->keys_amount 	  = (int)$planData->keys_amount;
		$planPurchase->price 		  = (float)$this->plan->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();

    	/** Add skeleton keys in user's table **/
    	$availableSkeletonLeys = (new UserRepository)->addSkeletonKeyInAccount($user, $this->plan->keys_amount);
    	
    	/** return the available skeleton keys **/
    	return ['available_skeleton_keys'=> $availableSkeletonLeys];
    }
}