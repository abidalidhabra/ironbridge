<?php

namespace App\Factories;

use App\Repositories\PlanPurchase\FivePlusSkeletonPurchase;
use App\Repositories\PlanPurchase\GoldPurchase;
use App\Repositories\PlanPurchase\GoldSkeletonPurchase;
use App\Repositories\PlanPurchase\SkeletonPurchase;
use App\Repositories\PlanRepository;
use Exception;

class PlanPurchaseFactory
{

	public function initializePlanPurchase($planData, $user){

		/** Get the plan info by id **/
		$plan = (new PlanRepository)->findPlanById($planData->plan_id);

		if ($plan->type == 'gold') {
			return new GoldPurchase($plan, $user);
		}else if ($plan->type == 'skeleton' && $plan->skeleton_keys_amount >= 5) {
			return new FivePlusSkeletonPurchase($plan, $user);
		}else if ($plan->type == 'skeleton') {
			return new SkeletonPurchase($plan, $user);
		}else if ($plan->type == 'both') {
			return new GoldSkeletonPurchase($plan, $user);
		}
		
		throw new Exception("Unsupported payment purchase");
	}
}