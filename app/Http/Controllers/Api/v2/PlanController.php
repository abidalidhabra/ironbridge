<?php

namespace App\Http\Controllers\Api\v2;

use App\Factories\PlanPurchaseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\PlanRequest;
use App\Http\Requests\v2\SkeletonPurchaseRequest;
use App\Models\v1\Country;
use App\Models\v2\Plan;
use App\Repositories\PlanRepository;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    
    public function createAPurchase(PlanRequest $request)
    {
    	try {

    		$user = auth()->user();
    		$planPurchaseFactory = new PlanPurchaseFactory();
    		$plan 	= $planPurchaseFactory->initializePlanPurchase($request, $user);
    		$result = $plan->create($request);
    		
    		return response()->json(['message'=> 'Plan purchase has been mad successfully.', 'data'=> $result]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }

    public function buySkeletonFromGold(SkeletonPurchaseRequest $request)
    {
    	try {
            $userRepository = new UserRepository(auth()->user());
            $availableSkeletonKeys = $userRepository->buySkeletonKeysFromGold($request);
            
	    	return response()->json(['message'=> 'Skeleton key has been added successfully to your account.', 'data'=> $availableSkeletonKeys]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }
}
