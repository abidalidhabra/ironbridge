<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\TreasureLocation;
use Validator;
use App\Models\v1\PlaceStar;

class LocationController extends Controller
{
    public function getParks1(Request $request)
    {
    	$location = TreasureLocation::select('latitude','longitude','place_name','place_id','boundary_arr','boundingbox')			->with('complexities:_id,place_id')
    								->whereIn('city',['Calgary'])
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
        \Log::info($request->data);
        $validator = Validator::make($request->all(),[
                        'data'       => "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()]);
        }


    	$data  = json_decode($request->get('data'),true);
    	
    	$id = $data[0]['_id'];
    	$location = TreasureLocation::where('_id',$id)
                                ->first();
    	foreach ($data[0]['clues'] as $key => $value) {
            if (isset($value) && !empty($value)) {
            	$complexity = $location->complexities()->updateOrCreate([
        									'place_id'=>$id,
        									'complexity'=>$key
        								],[
        									'place_id'=>$id,
        									'complexity'=>$key
        								]);
    	        $complexity->place_clues()->updateOrCreate(['place_star_id'=>$complexity->_id],['place_star_id'=>$complexity->_id,'coordinates'=>$value]);
            }
    	}

    	$subject ="updated clues";
        $email = 'arshikweb@gmail.com';
        //$email = 'abidalidhabra@gmail.com';
        $from="support@ironbridge1779.com";
        $message = $request->get('data');
        $headers = "From:".$from;
        mail($email,$subject,$message,$headers);
        
        return response()->json(['message' => 'Location has been updated successfully']); 
    }

    //GET LOCATION
    public function getLocation(Request $request){
        $validator = Validator::make($request->all(),[
                        'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $clue = $request->get('star');

        $location = TreasureLocation::select('latitude','longitude','custom_name')
                                    ->whereHas('complexities', function ($query) use ($clue) {
                                        $query->where('complexity',(int)$clue);
                                    })
                                    ->get();

        return response()->json($location);
    }

    //Get Clue
    public function getClue(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:new_city_info,_id",
                        'star'   => "required|integer|between:1,5",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $huntId  = $request->get('hunt_id');
        $clueId  = (int)$request->get('star');

        $clue = PlaceStar::select('place_id','complexity')
                        ->where([
                                    'place_id'   => $huntId,
                                    'complexity' => $clueId
                                ])
                        ->with('place_clues:_id,place_star_id,coordinates')
                        ->with('place:_id,custom_name,latitude,longitude,place_name')
                        ->first();
        if ($clue) {
            $data = [
                        'custom_name' => $clue->place->custom_name,
                        'place_name' => $clue->place->place_name,
                        'latitude' => $clue->place->latitude,
                        'longitude' => $clue->place->longitude,
                        'clue' => $clue->place_clues->coordinates,
                    ];
        } else {
            $data = [];
        }
        return response()->json($data);
    }
}
