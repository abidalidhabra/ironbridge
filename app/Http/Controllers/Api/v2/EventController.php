<?php

namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\v2\Event;
use App\Models\v1\City;
use Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    /* GET CITY LIST */
    public function getEventCityList(Request $request)
    {
    	$todayDate = Carbon::today();

    	$cityList = City::select('_id','name')
    					->whereHas('events',function($query) use ($todayDate){
    						$query->where('starts_at','>=',$todayDate);
    					})
    					->get();
        return response()->json(['message'=> 'City has been retrieved successfully.', 'data'=> $cityList]);
    	
    }

    public function getCityEventDetails(Request $request){
    	$validator = Validator::make($request->all(),[
                        'city_id'=> "required|exists:cities,_id",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $cityId = $request->get('city_id'); 
    	$todayDate = Carbon::today();

        $events = Event::select('_id','name','fees','description','starts_at','ends_at','discount','city_id')
        				->where('city_id',$cityId)
        				->where('starts_at','>=',$todayDate)
        				->with('prizes')
        				->get()
        				->map(function($query){
        					// if ($query->prizes) {
        					// 	$query->prizes;
        					// }
					        // exit();
					        return $query;
        				});
       
        
        return $events;
    }
}
