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
    	
    	$location = Hunt::select('location','place_name','place_id','boundaries_arr','boundingbox')
                                    ->with('hunt_complexities:_id,hunt_id')
    								->whereIn('city',['Calgary','Vancouver'])
    								->get()
    								->map(function($query){
    									if (count($query->hunt_complexities) > 0) {
    										$query->clue = true;
    									} else {
    										$query->clue = false;
    									}
    									$location = $query->location['coordinates'];
                                    	$query->latitude = $location[1];
                                    	$query->longitude = $location[0];
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
                                                $latlng[0],
                                                $latlng[1]
                                            ];
                    $huntComplexities->hunt_clues()->updateOrCreate([
                                        'hunt_complexity_id' =>  $huntComplexities->_id,
                                        'location.coordinates.0' =>  $latlng[0],
                                        'location.coordinates.1' =>  $latlng[1],
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
        $location = Hunt::select('location','name','place_name')
                                    ->whereHas('hunt_complexities', function ($query) use ($clue) {
                                        $query->where('complexity',(int)$clue);
                                    })
                                    ->get()
                                    ->map(function($query){
                                    	$location = $query->location['coordinates'];
                                    	$query->latitude = $location[1];
                                    	$query->longitude = $location[0];
                                        $query->name = ($query->name != "")?$query->name:$query->place_name;
                                    	unset($query->location);
                                    	return $query;
                                    });

        return response()->json(['message'=>'Hunt has been retrieved successfully','data'=>$location]);
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
                $huntClues[] = [$value->location['coordinates'][0],$value->location['coordinates'][1]];
            }
        }
        
        $data = [
                    'custom_name' => $hunt->name,
                    'place_name' => $hunt->place_name,
                    'latitude' => $location[1],
                    'longitude' => $location[0],
                    'clue' => $huntClues,
                ];

        return response()->json($data);
    }

    public function getHuntClue(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'  => "required|exists:hunts,_id",
                        'star'     => "required|integer|between:1,5",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $huntId  = $request->get('hunt_id');
        $clueId  = (int)$request->get('star');

        $huntUser = HuntUser::select('hunt_id')
                            ->with('hunt_user_details:_id,finished_in,hunt_user_id')
                            ->where('hunt_id',$huntId)
                            ->get()
                            ->map(function($query){
                                $finishedIn = $query->hunt_user_details->pluck('finished_in')->toArray();
                                if($finishedIn){
                                    $array_finished = array_diff($finishedIn,array(0));
                                    if ($array_finished) {
                                        $query->finished_in = min(array_diff($finishedIn,array(0)));
                                    } else {
                                        $query->finished_in = 0;
                                    }
                                    
                                }
                                unset($query->hunt_user_details);
                                return $query;
                            });

        
        $hunt = Hunt::select('_id','name','location','place_name','fees')
                    ->with(['hunt_complexities'=>function($query) use ($clueId){
                        $query->where('complexity',$clueId)
                            ->select('hunt_id','complexity')
                            ->with('hunt_clues:_id,hunt_complexity_id,location,game_id,game_variation_id,est_completion');
                    }])
                    ->where('_id',$huntId)
                    ->first();
        $location = $hunt->location['coordinates'];
        
        $huntClues = [];
        $est_completion = "00:00:00";
        if (count($hunt->hunt_complexities) > 0) {
            foreach ($hunt->hunt_complexities[0]->hunt_clues as $key => $value) {
                $huntClues[] = [$value->location['coordinates'][0],$value->location['coordinates'][1]];
            }

            $est_sec = $hunt->hunt_complexities[0]->hunt_clues->pluck('est_completion')->toArray();
            $est_completion = gmdate("H:i:s", array_sum($est_sec));
        }

        $bestTime = "00:00:00";
        if (count($huntUser->pluck('finished_in')) > 0) {
            $bestTime = gmdate("H:i:s", min($huntUser->pluck('finished_in')->toArray()));
        }
        
        $data = [
                    'hunt_id'       => $hunt->id,
                    'hunt_name'     => ($hunt->name != "")?$hunt->name:$hunt->place_name,
                    'latitude'      => $location[0],
                    'longitude'     => $location[1],
                    'clue'          => $huntClues,
                    'est_complete'  => $est_completion,
                    'best_time'     => $bestTime,
                    'distance'      => 0,
                    'cost'          => $hunt->fees,
                    'complexity'    => $clueId,
                ];

        return response()->json(['message'=>'Hunt clues has been retrieved successfully','data'=>$data]);
    }

    //JOIS HUNT
    public function joinHunt(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'=> "required",
                        'hunt_mode'=>'required|in:challenge,normal'
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();

        
        $data = $request->all();
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');
        $huntMode = $request->get('hunt_mode');
        $huntComplexitie = HuntComplexitie::with('hunt_clues')
                                            ->with('hunt:_id,name,fees')
                                            ->where([
                                                        'complexity' => $star,
                                                        'hunt_id'    => $huntId,
                                                    ])
                                            ->first();

        $huntUserDetail = HuntUser::select('user_id','hunt_id','hunt_complexity_id','status','hunt_mode','skeleton')
                                    //->with('hunt_user_details') 
                                    ->where([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                    ])
                            ->first();

        if($huntUserDetail){
            return response()->json([
                                'message'=>'User already exists',
                            ],422);
        } else {
            $skeleton = [];
            $huntUser = HuntUser::create([
                                            'user_id'            => $user->_id,
                                            'hunt_id'            => $huntId,
                                            'hunt_complexity_id' => $huntComplexitie->id,
                                            'valid'              => false,
                                            'status'             => 'progress',
                                            'hunt_mode'          => $request->get('hunt_mode'),
                                        ]);

            foreach ($huntComplexitie->hunt_clues as $key => $value) {
                $huntUserDetail = HuntUserDetail::create([
                                                            'hunt_user_id'      => $huntUser->id,
                                                            'location'          => $value->location,
                                                            'game_id'           => $value->game_id,
                                                            'game_variation_id' => $value->game_variation_id,
                                                            'est_completion'    => $value->est_completion,
                                                            'revealed_at'       => null,
                                                            'finished_in'       => 0,
                                                            'status'            => 'progress'
                                                        ]);
                $skeleton[] = [
                                'key'   => substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 10)), 0, 10),
                                'used' => false
                            ];
            }
            $huntUser->skeleton = $skeleton;
            $huntUser->save();
            if ($huntMode == 'challenge') {
                $coin = $user->gold_balance - $huntComplexitie->hunt->fees;
                $user->gold_balance = $coin;
                $user->save();            
            }
            return response()->json([
                                'message'=>'user has been successfully participants'
                            ]);
        }   
    }

    //GET HUNT USER
    public function getHuntUser(Request $request){
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

        $huntUser = HuntUser::select('_id','user_id','hunt_id','hunt_complexity_id','status','skeleton')
                            ->where([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                    ])
                            ->with('hunt_user_details:_id,hunt_user_id,location,est_completion,status')
                            ->first();
        $huntUser->skeleton_used = false;
        foreach ($huntUser->skeleton as $key => $value) {
            if ($value['used'] == false) {
                $huntUser->skeleton_used = true;
            }    
        }
        unset($huntUser->skeleton);
        return response()->json([
                                'message' => 'hunt user has been retrieved successfully',
                                'data'    => $huntUser
                            ]);
    }

    //HUT LIST
    public function huntList(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'   => "required|integer|between:1,5",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $huntId  = $request->get('hunt_id');
        $starId  = (int)$request->get('star');

        $hunt = HuntComplexitie::select('hunt_id','complexity')
                                ->where('complexity',$starId)
                                ->where('hunt_id','!=',$huntId)
                                ->with('hunt:_id,name,place_name')
                                ->get()
                                ->map(function($query){
                                    $query->hunt_name = ($query->hunt->name != "")?$query->hunt->name:$query->hunt->place_name;
                                    unset($query->hunt);
                                    return $query;
                                });
        
        
        return response()->json([
                                'message' => 'hunt has been retrieved successfully',
                                'data'    => $hunt
                            ]);
    }

    //HUNT PAUSE LIST
    public function huntPauseList(Request $request){
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');
        
        $user = Auth::User();
        $huntUser = HuntUser::select('_id','user_id','hunt_id','hunt_complexity_id','status')
                            ->with(['hunt:_id,name,place_name,location','hunt_complexities:_id,complexity'])
                            ->where([
                                        'user_id' => $user->_id,
                                        'status'  => 'pause',
                                    ])
                            ->get()
                            ->map(function($query){
                                $query->hunt_name = ($query->hunt->name != "")?$query->hunt->name:$query->hunt->place_name;
                                $query->location = $query->hunt->location;
                                $query->star = $query->hunt_complexities->complexity;
                                
                                unset($query->hunt,$query->hunt_complexities);
                                return $query;
                            });
        return response()->json([
                                'message' => 'hunt has been retrieved successfully',
                                'data'    => $huntUser
                            ]);
    }
}
