<?php

namespace App\Factories;

use App\Repositories\PlanPurchase\GoldPurchase;
use App\Repositories\PlanPurchase\SkeletonPurchase;
use App\Repositories\PlanRepository;
use Exception;

class PlanPurchaseFactory
{

	public function initializePlanPurchase($planData){

		/** Get the plan info by id **/
		$plan = (new PlanRepository)->findPlanById($planData->plan_id);

		if ($plan->type == 'gold') {
			return new GoldPurchase($plan);
		}else if ($plan->type == 'skeleton') {
			return new SkeletonPurchase($plan);
		}
		
		throw new Exception("Unsupported payment purchase");
	}
}