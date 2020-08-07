<?php

namespace App\Repositories;

use App\Models\v2\Plan;

class PlanRepository extends ModelRepository
{
	
	protected $model;

    public function __construct()
    {
        $this->model = new Plan;
    }
    
    public function findPlanById($id)
    {
        return Plan::where('_id', $id)->firstOrFail();
    }
}