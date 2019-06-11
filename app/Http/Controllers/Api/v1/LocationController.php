<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\TreasureLocation;
use Validator;

class LocationController extends Controller
{
    public function getParks1(Request $request)
    {
    	$location = TreasureLocation::select('latitude','longitude','place_name','place_id','boundary_arr','boundingbox')			->with('complexities:_id,place_id')
    								->whereIn('city',['Calgary','Vancouver'])
    								->get()
    								->map(function($query){
    									if (count($query->complexities) > 0) {
    										$query->clue = true;
    									} else {
    										$query->clue = false;
    									}
										unset($query->complexities);
    									return $query;
    								});
        return response()->json($location);
        //return response()->json(CityInfo::all());
    }

    //UPDATE LOCATION
    public function updateClues(Request $request){
        $validator = Validator::make($request->all(),[
                        'data'       => "required|json",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()], 422);
        }

    	$data  = json_decode($request->get('data'),true);
    	
    	$id = $data[0]['_id'];
    	$location = TreasureLocation::where('_id',$id)
                                ->first();
    	foreach ($data[0]['clues'] as $complexity => $value) {
    		$complexity = $location->complexities()->updateOrCreate([
    									'place_id'=>$id,
    									'complexity'=>$complexity
    								],[
    									'place_id'=>$id,
    									'complexity'=>$complexity
    								]);
	        $complexity->place_clues()->updateOrCreate(['place_star_id'=>$complexity->_id],['place_star_id'=>$complexity->_id,'coordinates'=>$value]);
    	}
    	
        return response()->json(['status'=>true,'message' => 'Location has been updated successfully']); 
    }

    //GET LOCATION
    public function getLocation(Request $request){
        $validator = Validator::make($request->all(),[
                        'clue'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()]);
        }

        $clue = $request->get('clue');

        $location = TreasureLocation::select('latitude','longitude','custom_name')
                                    ->whereHas('complexities', function ($query) use ($clue) {
                                        $query->where('complexity',(int)$clue);
                                    })
                                    ->get();

        return response()->json($location);
    }
}
