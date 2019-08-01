<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\PlanPurchaseInterface;
use App\Repositories\User\UserRepository;

class GoldPurchase implements PlanPurchaseInterface
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
		$planPurchase->gold_value 	  = (int)$this->plan->gold_value;
		$planPurchase->price 		  = (float)$planData->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();

    	/** Add gold value in user's table **/
    	$userRepository = new UserRepository($this->user);
    	$availableGoldBalance = $userRepository->addGold($this->plan->gold_value);
    	
    	/** return the available gold balance **/
    	return ['available_gold_balance'=> $availableGoldBalance];
    }
}
