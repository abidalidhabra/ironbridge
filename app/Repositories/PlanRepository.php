<?php

namespace App\Repositories;

use App\Models\v2\Plan;

class PlanRepository
{
	
    public function findPlanById($id)
    {
        return Plan::where('_id', $id)->firstOrFail();
    }
}