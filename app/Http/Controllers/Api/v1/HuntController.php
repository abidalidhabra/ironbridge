<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Hunt;
use App\Models\v1\HuntComplexitie;
use App\Models\v1\HuntUser;
use App\Models\v1\HuntUserDetail;
use App\Models\v1\Game;
use Validator;
use Auth;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;

class HuntController extends Controller
{
    public function __construct()
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set( 'serialize_precision', -1 );
        }
    }
    
    //GET HUNT
    public function getParks1(Request $request)
    {
    	
    	$location = Hunt::select('location','place_name','place_id','boundaries_arr','boundingbox')
                                    ->with('hunt_complexities:_id,hunt_id')
    								->whereIn('city',['Vancouver'])
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
        /*$subject ="updated clues";
        $email = 'arshikweb@gmail.com';
        //$email = 'abidalidhabra@gmail.com';
        $from="support@ironbridge1779.com";
        $message = $request->get('data');
        $headers = "From:".$from;
        mail($email,$subject,$message,$headers);
        */

        $validator = Validator::make($request->all(),[
                        'data'       => "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()]);
        }

        $data  = json_decode($request->get('data'),true);
        $id = $data['_id'];
        $hunt = Hunt::where('_id',$id)->first();
        if (!$hunt) {
            return response()->json(['message' => 'Hunt not found successfully'],422); 
        }
        foreach ($data['clue_data'] as $key => $value) {
            if (isset($value) && !empty($value) && count($value)>0) {
                if (!empty($value['total_clues']) && count($value['total_clues']) > 0) {
                    $distance = (int)round($value['distance']);
                    $km = $distance/1000;
                    //4.5 km = 6o min
                    // $avg_km = $km/4.5;   
                    $mins = 60/4.5 * $km;
                    $fixClueMins = count($value['total_clues'])*5;
                    $estTime =  $mins + $fixClueMins;
                    $huntComplexities = $hunt->hunt_complexities()->updateOrCreate(['hunt_id'=>$id,'complexity'=>$key],['hunt_id'=>$id,'complexity'=>$key,'distance'=>$distance,'est_completion'=>(int)round($estTime)]);
                    foreach ($value['total_clues'] as $latlng) {
                        $game = Game::whereHas('game_variation')
                                    ->with('game_variation')
                                    ->get()
                                    ->random(1);
                        
                        $rand_variation = rand(0,$game[0]['game_variation']->count()-1);
                        $gameVariationId = $game[0]['game_variation'][$rand_variation]->id;

                        $location['Type'] = 'Point';
                        $location['coordinates'] = [
                                                    $latlng[1],
                                                    $latlng[0]
                                                ];
                     
                        $huntComplexities->hunt_clues()->updateOrCreate([
                                            'hunt_complexity_id' =>  $huntComplexities->_id,
                                            'location.coordinates.0' =>  $latlng[0],
                                            'location.coordinates.1' =>  $latlng[1],
                                        ],[
                                            'hunt_complexity_id' => $huntComplexities->_id,
                                            'location'           => $location,
                                            'game_id'            => $game[0]->id,
                                            'game_variation_id'  => $gameVariationId
                                        ]);
                    }
                }

            }
        }

        
        
        return response()->json(['message' => 'Location has been updated successfully']); 
    }

    //GET LOCATION
    public function getHuntsByDifficulty(Request $request){
        $validator = Validator::make($request->all(),[
                        'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $clue = $request->get('star');
        $user = Auth::User();
        $userId = $user->id;
        $location = Hunt::select('location','name','place_name')
                                    ->whereHas('hunt_complexities', function ($query) use ($clue,$userId) {
                                        $query->where('complexity',(int)$clue);
                                    })
                                    ->with(['hunt_complexities'=>function($query) use ($clue,$userId){
                                        $query->where('complexity',(int)$clue)
                                        ->with(['hunt_users'=>function($hunt) use ($userId){
                                            $hunt->where('user_id',$userId);
                                        }]);
                                    }])
                                    ->get()
                                    ->map(function($query){
                                    	$location = $query->location['coordinates'];
                                    	$query->latitude = $location[1];
                                    	$query->longitude = $location[0];
                                        $query->name = ($query->name != "")?$query->name:$query->place_name;
                                    	if (count($query->hunt_complexities[0]->hunt_users)>0 && $query->hunt_complexities[0]->hunt_users[0]->status == 'participate') {
                                            $query->status = true;
                                        } else {
                                            $query->status = false;
                                        }
                                        unset($query->location,$query->hunt_complexities);
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

    public function getHuntDetails(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'  => "required|exists:hunts,_id",
                        'star'     => "required|integer|between:1,5",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }
        /**/
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
                    ->whereHas('hunt_complexities',function($query) use ($clueId){
                        $query->where('complexity',$clueId);
                    })
                    ->with(['hunt_complexities'=>function($query) use ($clueId){
                        $query->where('complexity',$clueId)
                            ->select('hunt_id','complexity','est_completion')
                            ->with('hunt_clues:_id,hunt_complexity_id,location,game_id,game_variation_id,est_completion');
                    }])
                    ->where('_id',$huntId)
                    ->first();
        
        
        if (!$hunt) {
           return response()->json(['message'=>'Hunt details not found'],422);
        }
        $huntClues = [];
        $location = $hunt->location['coordinates'];
        /*print_r($hunt->hunt_complexities->toArray());
        exit();*/
        $est_completion = '00:00:00';
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
                    'name'          => ($hunt->name != "")?$hunt->name:$hunt->place_name,
                    'latitude'      => $location[1],
                    'longitude'     => $location[0],
                    'clue'          => $huntClues,
                    'total_clue'    => count($huntClues),
                    'est_complete'  => $est_completion,
                    'best_time'     => $bestTime,
                    'cost'          => $hunt->fees,
                    'complexity'    => $clueId,
                ];

        return response()->json(['message'=>'Hunt clues has been retrieved successfully','data'=>$data]);
    }

    //JOIS HUNT
    public function participateInHunt(Request $request)
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
        if(!$huntComplexitie){
            return response()->json([
                                'message'=>'Hunt details not found',
                            ],422);
        }

        $huntUserDetail = HuntUser::select('user_id','hunt_id','hunt_complexity_id','status','hunt_mode','skeleton') 
                                    ->where([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                        //'hunt_mode'          => $huntMode,
                                    ])
                            ->first();

        if($huntUserDetail){
            return response()->json([
                                'message'=>'You already participated in this hunt.',
                            ],422);
        } else {
            $skeleton = [];
            $huntUser = HuntUser::create([
                                            'user_id'            => $user->_id,
                                            'hunt_id'            => $huntId,
                                            'hunt_complexity_id' => $huntComplexitie->id,
                                            'valid'              => false,
                                            'status'             => 'participate',
                                            'hunt_mode'          => $huntMode,
                                            'started_at'         => null,
                                            'ended_at'           => null,
                                            'est_completion'     => $huntComplexitie->est_completion,
                                        ]);

            foreach ($huntComplexitie->hunt_clues as $key => $value) {
                $huntUserDetail = HuntUserDetail::create([
                                                            'hunt_user_id'      => $huntUser->id,
                                                            'location'          => $value->location,
                                                            'game_id'           => $value->game_id,
                                                            'game_variation_id' => $value->game_variation_id,
                                                            'revealed_at'       => null,
                                                            'finished_in'       => 0,
                                                            'status'            => 'participate',
                                                            'started_at'        => null,
                                                            'ended_at'          => null,
                                                        ]);
                $skeleton[] = [
                                'key'   => substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 10)), 0, 10),
                                'used'  => false ,
                                'used_date'=>null
                            ];
            }
            $huntUser->skeleton = $skeleton;
            $huntUser->save();
            if ($huntMode == 'challenge') {
                $coin = $user->gold_balance - $huntComplexitie->hunt->fees;
                $user->gold_balance = $coin;
                $user->save();            
            }

            $request->request->add(['hunt_user_id'=>$huntUser->id]);
            $data1 = (new HuntController)->getHuntParticipationDetails($request);
            
            return response()->json([
                                'message'=>'user has been successfully participants',
                                'data'  => $data1->original['data']
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


    public function getHuntParticipationDetails(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_id'=> "required|exists:hunt_users,_id",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::user();
        
        $data = $request->all();
        $huntUserId = $request->get('hunt_user_id');

        $huntUser = HuntUser::select('_id','user_id','hunt_id','hunt_complexity_id','status','skeleton')
                            ->where('_id',$huntUserId)
                            //->with('hunt_user_details:_id,hunt_user_id,location,est_completion,status')
                            ->with(['hunt_user_details'=>function($query) use ($huntUserId){
                                $query->where('hunt_user_id',$huntUserId)
                                    ->select('_id','hunt_user_id','location','est_completion','status','game_id','game_variation_id')
                                    ->with('game:_id,name,identifier','game_variation');
                            }])
                            ->first();
        
        $huntUser->skeleton_key_available = false;
        foreach ($huntUser->skeleton as $key => $value) {
            if ($value['used'] == false) {
                $huntUser->skeleton_key_available = true;
            }    
        }
        unset($huntUser->skeleton);
        return response()->json([
                                'message' => 'hunt user has been retrieved successfully',
                                'data'    => $huntUser
                            ]);
    }

    //HUT LIST
    public function getNearByHunts(Request $request){
        $validator = Validator::make($request->all(),[
                        'star'   => "required|integer|between:1,5",
                        'page' => 'required|numeric|min:1',
                        'longitude' => 'required|numeric',
                        'latitude' => 'required|numeric'
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $star       = (int)$request->star;
        $page       = (int)$request->page;
        $user       = Auth::User();
        $longitude  = (float)$request->longitude;
        $latitude   = (float)$request->latitude;
        $page       = $page -1;
        $take       = 10;
        $skip       = ($page * $take);
        $distance   = 100;
        // $latitude   = (float)$user->location['coordinates'][0];
        // $longitude  = (float)$user->location['coordinates'][1];

        $huntIds = HuntUser::where('user_id',$user->id)->pluck('hunt_id')->toArray();
        $hunts = Hunt::raw(function($collection) use ($latitude,$longitude,$distance,$star,$skip,$take,$huntIds)
                            {
                                return $collection->aggregate([
                                    [ 
                                        '$geoNear' => [ 
                                            'near' => [
                                                'type' => 'Point',
                                                'coordinates' => [$longitude,$latitude]
                                            ],
                                            'spherical' => true,
                                            'distanceField' => 'distance',
                                            'distanceMultiplier'=> 0.001,
                                            "includeLocs" => "location.coordinates",
                                            "maxDistance"=> $distance * 1000,
                                        ]
                                    ],
                                    [
                                        '$match' => [
                                            '_id' => ['$nin' => $huntIds]
                                        ]
                                    ],
                                    [
                                        '$addFields' => [
                                            'hunt_string_id' => [
                                                '$toString' => '$_id'
                                            ]
                                        ]
                                    ],
                                    [
                                        '$lookup' => [
                                            'from' => 'hunt_complexities',
                                            'let'=> [ 'hunt_string_id'=> '$hunt_string_id'],
                                            'pipeline'=> [
                                                [
                                                    '$match'=> [ 
                                                        '$expr'=> [ 
                                                            '$and'=> [
                                                               [ '$eq'=> [ '$hunt_id',  '$$hunt_string_id' ] ],
                                                               [ '$eq'=> [ '$complexity', $star ] ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    '$project' => [
                                                        '_id' => [ '$toString' => '$_id' ],
                                                        'hunt_id' => true,
                                                        'complexity' => true,
                                                    ]
                                                ]
                                            ],
                                            'as' => 'hunt_complexities'
                                        ]
                                    ],
                                    [
                                        '$lookup' => [
                                            'from' => 'hunt_users',
                                            'let'=> [ 'hunt_string_id'=> '$hunt_string_id'],
                                            'pipeline'=> [
                                                [
                                                    '$match'=> [ 
                                                        '$expr'=> [ 
                                                            '$and'=> [
                                                               [ '$eq'=> [ '$hunt_id',  '$$hunt_string_id' ] ],
                                                               [ '$eq'=> [ '$complexity', $star ] ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    '$project' => [
                                                        '_id' => [ '$toString' => '$_id' ],
                                                        'hunt_id' => true,
                                                        'complexity' => true,
                                                    ]
                                                ]
                                            ],
                                            'as' => 'hunt_complexities'
                                        ]
                                    ],
                                    [
                                        '$sort' => [
                                            'distance' => 1
                                        ]
                                    ],
                                    ['$skip' => $skip],
                                    ['$limit' => $take+1],
                                    [
                                        '$project' => [
                                            '_id' => true,
                                            'name' => true,
                                            'location' => true,
                                            'distance' => true,
                                        ]
                                    ]
                                ]);
                            });

        $hasNextPage = ($hunts->count() > 20)?true:false;
        return response()->json([
                                'message' => 'Near by hunts has been retrieved successfully.',
                                'data'    => $hunts,
                                'has_next_page' => $hasNextPage
                            ]);
        print_r($hunt->toArray());
        exit;
        $huntId = $hunt->pluck('id'); 
        
        $hunt_data = Hunt::select('_id','name','place_name')
                    ->whereHas('hunt_complexities',function($query) use ($starId){
                        $query->where('complexity',$starId);
                    })
                    ->whereIn('_id',$huntId)
                    ->get()
                    ->map(function($query) use ($starId , $hunt){
                        $query->hunt_name = ($query->name != "")?$query->name:$query->place_name;
                        $query->hunt_id = $query->_id;
                        $query->complexity = $starId;
                        unset($query->place_name,$query->name);
                        return $query;
                    });
        
    }

    //HUNT PAUSE LIST
    public function getHuntsInProgress(Request $request){

        $user = Auth::User();
        
        $huntUser = HuntUser::whereHas('hunt_user_details',function($query){
                                $query->whereIn('status',['pause','progress']);
                            })
                            ->select('_id','user_id','hunt_id')
                            ->with(['hunt:_id,name,place_name,location'])
                            ->where([
                                        'user_id' => $user->_id,
                                    ])
                            ->get()
                            ->map(function($query){
                                $query->name = ($query->hunt->name != "")?$query->hunt->name:$query->hunt->place_name;
                                $query->location = $query->hunt->location;
                                //$query->star = $query->hunt_complexities->complexity;
                                
                                unset($query->hunt , $query->user_id);
                                return $query;
                            });

        return response()->json([
                                'message' => 'hunt has been retrieved successfully',
                                'data'    => $huntUser
                            ]);
    }
}
