<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Hunt;
use App\Models\v1\HuntComplexitie;
use Validator;

class HuntController extends Controller
{
    //GET HUNT
    public function getParks1(Request $request)
    {
    	
    	$location = Hunt::select('location','place_name','place_id','boundaries_arr','boundingbox')			->with('hunt_complexities:_id,hunt_id')
    								->whereIn('city',['Calgary','Vancouver'])
    								->get()
    								->map(function($query){
    									if (count($query->hunt_complexities) > 0) {
    										$query->clue = true;
    									} else {
    										$query->clue = false;
    									}
    									$location = $query->location['coordinates'];
                                    	$query->latitude = $location['lat'];
                                    	$query->longitude = $location['lng'];
										unset($query->hunt_complexities,$query->location);
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
        $hunt = Hunt::where('_id',$id)->first();
        
        foreach ($data[0]['clues'] as $key => $value) {
            if (isset($value) && !empty($value)) {
                $huntComplexities = $hunt->hunt_complexities()->updateOrCreate(['hunt_id'=>$id,'complexity'=>$key],['hunt_id'=>$id,'complexity'=>$key]);
                foreach ($value as $latlng) {
                    $location['Type'] = 'Point';
                    $location['coordinates'] = [
                                                'lng' => $latlng[0],
                                                'lat' => $latlng[1]
                                            ];
                    $huntComplexities->hunt_clues()->updateOrCreate([
                                        'hunt_complexity_id' =>  $huntComplexities->_id,
                                        'location.coordinates.lng' =>  $latlng[0],
                                        'location.coordinates.lat' =>  $latlng[1],
                                    ],[
                                        'hunt_complexity_id' => $huntComplexities->_id,
                                        'location'           => $location,
                                        'game_id'            => null,
                                        'game_variation_id'  => null
                                    ]);
                }

            }
        }

        // $subject ="updated clues";
        // $email = 'arshikweb@gmail.com';
        // //$email = 'abidalidhabra@gmail.com';
        // $from="support@ironbridge1779.com";
        // $message = $request->get('data');
        // $headers = "From:".$from;
        // mail($email,$subject,$message,$headers);
        
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
        $location = Hunt::select('location','name')
                                    ->whereHas('hunt_complexities', function ($query) use ($clue) {
                                        $query->where('complexity',(int)$clue);
                                    })
                                    ->get()
                                    ->map(function($query){
                                    	$location = $query->location['coordinates'];
                                    	$query->latitude = $location['lat'];
                                    	$query->longitude = $location['lng'];
                                    	unset($query->location);
                                    	return $query;
                                    });

        return response()->json($location);
    }


    //Get Clue
    public function getClue(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'   => "required|integer|between:1,5",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $huntId  = $request->get('hunt_id');
        $clueId  = (int)$request->get('star');

        $clue = HuntComplexitie::select('hunt_id','complexity')
                        ->where([
                                    'hunt_id'   => $huntId,
                                    'complexity' => $clueId
                                ])
                        ->with('hunt:_id,name,location,place_name')
                        ->with('hunt_clues:_id,hunt_complexity_id,location.coordinates')
                        ->first();
        
        $location = $clue->hunt->location['coordinates'];
        
        $huntClues = [];
        foreach ($clue->hunt_clues as $key => $value) {
            $huntClues[] = [$value->location['coordinates']['lat'],$value->location['coordinates']['lng']];
        }
        if ($clue) {
            $data = [
                        'custom_name' => $clue->hunt->name,
                        'place_name' => $clue->hunt->place_name,
                        'latitude' => $location['lat'],
                        'longitude' => $location['lng'],
                        'clue' => $huntClues,
                    ];
        } else {
            $data = [];
        }
        return response()->json($data);
    }
}
