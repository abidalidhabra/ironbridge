<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Validator;
use App\Models\v2\DiscountCoupon;
use Auth;
use Carbon\Carbon;

class DiscountCouponController extends Controller
{

	public function getDiscountCoupon(Request $request){
		$date =  Carbon::today();
		// $date = new Carbon();

		$discount = DiscountCoupon::select('discount_code','discount_types','number_of_uses','start_at','end_at','total_used_coupon')
                                    //where('discount_types','gold_credit')
                                    ->where(function($query) use ($date){
										$query->whereNull('start_at')
        										->orWhere(function($query1) use ($date){
        											$query1->where('start_at','<=',$date)
        											->where('end_at','>=',$date);
        										});
									})
        							->get()
                                    ->each(function($query){
                                        if ($query->number_of_uses == null) {
                                            return $query;
                                        } else if($query->number_of_uses > $query->total_used_coupon){
                                            return $query;
                                        }
                                    });


         return response()->json([
                                'message' => 'Coupon code has been retrieved successfully',
                                'data'    => $discount
                            ]);
        
	}

    public function useTheGoldCoupon(Request $request)
    {
        $request['discount_code'] = strtoupper($request['discount_code']);

    	$validator = Validator::make($request->all(),[
    		'coupon_id'=>'required|exists:discount_coupons,_id',
    	]);

    	if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()], 422);
        }

        $id = $request->get('coupon_id');
        $user = Auth::User();
        $userId = $user->id;

        $discount = DiscountCoupon::where('_id',$id)
        							->first();


        if(!is_null($discount->number_of_uses) && $discount->number_of_uses <= $discount->total_used_coupon){
            return response()->json(['message'=>'Coupon limited offer'], 422);
        }

        if ($discount->can_mutitime_use) {
            $discount->push('users_id',$userId,true);
            $discount->total_used_coupon = $discount->total_used_coupon+1;
        } else {
            if (in_array($userId, $discount->users_id)) {
                return response()->json(['message'=>'Coupon has been already exists'], 422);
            } else {
                $discount->push('users_id',$userId,true);
            }
        }

        
        $user->gold_balance = $user->gold_balance+$discount->discount;
        $user->save();
        $discount->save();

        return response()->json(['message'=>'Coupon applied successfully']);
    }
}
