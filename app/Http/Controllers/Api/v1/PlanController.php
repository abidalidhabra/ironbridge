<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Plan;
use App\Models\v1\UserTransaction;
use App\Models\v1\UserBalancesheet;
use UserHelper;
use Carbon\Carbon;
use Validator;
use Auth;

class PlanController extends Controller
{
    public function getThePlans(Request $request)
    {
    	$plans = Plan::select('_id', 'name', 'plan_amount', 'gold_value')->get();
    	return response()->json(['message' => 'Plans has been retrieved successfully.','data'  => $plans]);
    }

    public function purchaseTheGolds(Request $request)
    {
    	
    	/* Validate the parameters */
    	$validator = Validator::make($request->all(),[
    		'plan_id' => "required|string|exists:plans,_id",
    		'transaction_id' => "required|string",
    	]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first(),'amount'=>0], 422);
        }

        /** Get the parameters **/
        $user = Auth::user();
        $userId = Auth::user()->id;
        $planId = $request->get('plan_id');
        $transactionId = $request->get('transaction_id');
        
        /** Get the Plan Information **/
        $plan 	= Plan::find($planId);
        $planAmount = $plan->plan_amount;
        $goldValue = $plan->gold_value;

        /** Insert the purchase entry in for user's balancesheet **/
		$user->balance_sheet()->save(new UserBalancesheet([
        	'user_id'			=> $userId,
        	'happens_at'		=> 'COIN_PURCHASE',
        	'happens_because'	=> 'COIN_PURCHASE',
        	'balance_type'		=> 'CR',
        	'credit'			=> $planAmount,
        	'transaction_id' 	=> $transactionId,
        ]));

		/** Add the gold in user's account **/
        // $user->gold_balance += $goldValue;
		// $user->save();
        $user->increment('gold_balance',$goldValue);

		/** Return the response **/
    	return response()->json(['message' => 'You have successfully purchased plan.','amount'=>$goldValue]);
    }
}
