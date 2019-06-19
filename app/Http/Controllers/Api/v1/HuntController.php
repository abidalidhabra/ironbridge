<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Hunt;
use App\Models\v1\HuntComplexitie;
use App\Models\v1\HuntUser;
use App\Models\v1\HuntUserDetail;
use Validator;
use Auth;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;

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

        $hunt = Hunt::select('_id','name','location','place_name')
                    ->with(['hunt_complexities'=>function($query) use ($clueId){
                        $query->where('complexity',$clueId)
                            ->select('hunt_id','complexity')
                            ->with('hunt_clues:_id,hunt_complexity_id,location,game_id,game_variation_id');
                    }])
                    ->where('_id',$huntId)
                    ->first();
        $location = $hunt->location['coordinates'];
        
        $huntClues = [];
        if (count($hunt->hunt_complexities) > 0) {
            foreach ($hunt->hunt_complexities[0]->hunt_clues as $key => $value) {
                $huntClues[] = [$value->location['coordinates']['lng'],$value->location['coordinates']['lat']];
            }
        }
        
        $data = [
                    'custom_name' => $hunt->name,
                    'place_name' => $hunt->place_name,
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'clue' => $huntClues,
                ];

        return response()->json($data);
    }


    //JOIS HUNT
    public function joinHunt(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::user();
        
        $data = $request->all();
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');

        $huntComplexitie = HuntComplexitie::with('hunt_clues')
                                            ->where([
                                                        'complexity' => $star,
                                                        'hunt_id'    => $huntId,
                                                    ])
                                            ->first();

        $huntUser = HuntUser::create([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                        'valid'              => false,
                                    ]);

        foreach ($huntComplexitie->hunt_clues as $key => $value) {
            $huntUserDetail = HuntUserDetail::create([
                                                        'hunt_user_id'      => $huntUser->id,
                                                        'location'          => $value->location,
                                                        'game_id'           => $value->game_id,
                                                        'game_variation_id' => $value->game_variation_id,
                                                        'est_completion'    => $value->est_completion,
                                                        'revealed_at'       => null,
                                                        'started_at'        => null,
                                                        'finished_at'       => null
                                                    ]);
        }

        return response()->json([
                                'status'=>true,
                                'message'=>'user has been successfully participants'
                            ]);
    }


    //GET HUNT USER
    public function getHuntUser(Request $request){
        $user = Auth::User();
        $userId = $user->id;

        $huntUser = HuntUser::select('user_id','hunt_id','status')
                            ->where('user_id',$userId)
                            ->with('hunt:_id,name,place_name')
                            ->with('hunt_user_details:_id,hunt_user_id,location,est_completion,revealed_at,started_at,finished_at')
                            ->first();

        $est_completion = $huntUser->hunt_user_details->pluck('est_completion')->toArray();

        $data = [
                    'hunt_name'    => ($huntUser->hunt->name != "")?$huntUser->hunt->name:$huntUser->hunt->place_name,
                    'clue'         => $huntUser->hunt_user_details->count(),
                    'est_complete' => gmdate("H:i:s", array_sum($est_completion)*60),
                    'distance'     => ""
                ];

        return response()->json([
                                'status'  => true,
                                'message' => 'user has been retrieved successfully',
                                'data'    => $data
                            ]);
    }

    
}
