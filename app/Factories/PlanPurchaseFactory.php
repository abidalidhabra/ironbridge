<?php

namespace App\Factories;

use App\Repositories\PlanPurchase\ChestBucketPurchase;
use App\Repositories\PlanPurchase\CompassPurchase;
use App\Repositories\PlanPurchase\GoldPurchase;
use App\Repositories\PlanPurchase\GoldSkeletonPurchase;
use App\Repositories\PlanPurchase\SkeletonPurchase;
use App\Repositories\PlanPurchase\SkeletonsBucketPurchase;
use App\Repositories\PlanRepository;
use Exception;

class PlanPurchaseFactory
{

	public function initializePlanPurchase($planData, $user){

		/** Get the plan info by id **/
		$plan = (new PlanRepository)->findPlanById($planData->plan_id);

		if ($plan->type == 'chest_bucket') {
			return new ChestBucketPurchase($plan, $user);
		}else if ($plan->type == 'compass') {
			return new CompassPurchase($plan, $user);
		}else if ($plan->gold_value && $plan->skeleton_keys) {
			return new GoldSkeletonPurchase($plan, $user);
		}else if ($plan->gold_value) {
			return new GoldPurchase($plan, $user);
		}else if ($plan->skeletons_bucket) {
			return new SkeletonsBucketPurchase($plan, $user);
		}else if ($plan->skeleton_keys) {
			return new SkeletonPurchase($plan, $user);
		}
		
		throw new Exception("Unsupported payment purchase");
	}
}