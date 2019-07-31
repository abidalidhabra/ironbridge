<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\PlanPurchaseInterface;
use App\Repositories\UserRepository;

class GoldPurchase implements PlanPurchaseInterface
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
		$planPurchase->gold_value 	  = (int)$this->plan->gold_value;
		$planPurchase->price 		  = (float)$planData->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();

    	/** Add gold value in user's table **/
    	$userRepository = new UserRepository();
    	$availableGoldBalance = $userRepository->addTheGoldInAccount($user, $this->plan->gold_value);
    	
    	/** return the available gold balance **/
    	return ['available_gold_balance'=> $availableGoldBalance];
    }
}
