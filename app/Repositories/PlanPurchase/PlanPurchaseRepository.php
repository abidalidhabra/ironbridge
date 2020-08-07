<?php

namespace App\Repositories\PlanPurchase;

use App\Models\v2\PlanPurchase;
use App\Repositories\ModelRepository;

class PlanPurchaseRepository extends ModelRepository
{

	protected $model;

    public function __construct()
    {
        $this->model = new PlanPurchase;
    }
    
	/**
	 *
	 * Saves the resource in the database
	 *
	 * @param object $planData
	 * @return App\Models\v2\Plan
	 */
	public function create($planData, $user){

		$plan 			= new PlanPurchase();
		$plan->user_id 	= $user->id;
		$plan->plan_id 	= $planData->plan_id;
		$plan->country_code = $planData->country_code;
		$plan->save();

		return $plan;
	}
}