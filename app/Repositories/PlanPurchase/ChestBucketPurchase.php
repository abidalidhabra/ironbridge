<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\Purchase;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\ChestService;

class ChestBucketPurchase implements Purchase
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
		$planPurchase->gold_price     = (float)$this->plan->gold_price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();

    	/** Add Chest Capacity value in user's table **/
    	(new ChestService)->setUser($this->user)->expand($this->plan->bucket);
    	
        /** Deduct User Gold **/
        $goldBalance = (new UserRepository($this->user))->deductGold($this->plan->gold_price);

    	/** return the available skeleton keys **/
    	return ['chest_bucket'=> $this->user->buckets['chests'], 'available_gold_balance'=> $goldBalance];
    }
}
