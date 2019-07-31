<?php

namespace App\Repositories\PlanPurchase;

interface PlanPurchaseInterface
{
	
	public function create($planData, $user);
}