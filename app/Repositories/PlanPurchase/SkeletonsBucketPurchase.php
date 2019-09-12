<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\Purchase;
use App\Repositories\User\UserRepository;

class SkeletonsBucketPurchase implements Purchase
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
		$planPurchase->skeletons_bucket = (int)$this->plan->skeletons_bucket;
		$planPurchase->price 		  = (float)$this->plan->price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->save();
    	
        /** Add User Bucket **/
    	$availableSkeletonKeys = (new UserRepository($this->user))->addSkeletonsBucket($this->plan->skeletons_bucket);
        
        /** Deduct User Gold **/
        $goldBalance = (new UserRepository($this->user))->deductGold($this->plan->gold_price);
    	
    	/** return the available skeleton keys **/
    	return ['skeletons_bucket_size'=> $availableSkeletonKeys, 'available_gold_balance'=> $goldBalance];
    }
}