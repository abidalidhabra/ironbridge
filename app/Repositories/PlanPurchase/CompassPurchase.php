<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\PlanPurchase\Purchase;
use App\Repositories\User\UserRepository;
use App\Services\AssetsLogService;
use App\Services\Event\EventService;
use App\Services\User\CompassService;
use Exception;

class CompassPurchase implements Purchase
{

	protected $plan, $user;

	public function __construct($plan, $user)
	{
		$this->plan = $plan;
		$this->user = $user;
	}

    public function create($planData)
    {

        /** Log this purchase **/
        (new AssetsLogService('compass', 'store'))->setUser($this->user)->compasses($this->plan->compasses)->save();

    	/** Add purchase in the database **/
    	$planPurchase 				  = new PlanPurchase();
		$planPurchase->user_id 		  = $this->user->id;
		$planPurchase->plan_id 		  = $planData->plan_id;
		$planPurchase->country_code   = $planData->country_code;
		$planPurchase->gold_price     = (float)$this->plan->gold_price;
		$planPurchase->transaction_id = $planData->transaction_id;
		$planPurchase->compasses 	  = $this->plan->compasses;
		$planPurchase->save();

    	/** Add skeleton keys in user's table **/
    	$compassService = (new CompassService)->setUser($this->user)->add($this->plan->compasses);

        /** Deduct User Gold **/
        $userRepository = new UserRepository($this->user);
        $goldBalance = $userRepository->deductGold($this->plan->gold_price);

    	/** return the available skeleton keys **/
    	return [
    		'compasses'=> $compassService->response(), 
    		'available_gold_balance'=> $goldBalance, 
    		'compass_plan_occupied_this_week'=> $userRepository->compassPlanOccupiedThisWeek($compassService->event)
    	];
    }
}
