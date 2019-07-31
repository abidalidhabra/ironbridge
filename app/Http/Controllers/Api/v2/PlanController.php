<?php

namespace App\Http\Controllers\Api\v2;

use App\Factories\PlanPurchaseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\PlanRequest;
use App\Http\Requests\v2\SkeletonPurchaseRequest;
use App\Models\v1\Country;
use App\Models\v2\Plan;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    
    // public function getPlans(Request $request)
    // {
    // 	// $countryCode = auth()->user()->country_code;
    // 	$countryCode = 'CA';
    // 	$plans = Country::where('code', $countryCode)->whereHas('plans')->with('plans:_id,name,country_id,price,gold_value')->select('_id','code','currency_symbol')->get();
    // 	return response()->json(['message'=> 'Plans are retrived successfully.', 'data'=> $plans]);
    // }

    public function addPurchase(PlanRequest $request)
    {
    	try {

    		$user = auth()->user();
    		$planPurchaseFactory = new PlanPurchaseFactory();
    		$plan 	= $planPurchaseFactory->initializePlanPurchase($request);
    		$result = $plan->create($request, $user);
    		
    		return response()->json(['message'=> 'Plan purchase has been mad successfully.', 'data'=> $result]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }

    public function buySkeletonFromGold(SkeletonPurchaseRequest $request, UserRepository $userRepository)
    {
    	try {

            $availableSkeletonKeys = $userRepository->buySkeletonFromGold(auth()->user(), $request);
            
	    	return response()->json(['message'=> 'Skeleton key has been added successfully to your account.', 'data'=> $availableSkeletonKeys]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }
}
