<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\ClueController;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\HuntByDiffRequest;
use App\Http\Requests\v1\HuntDetailRequest;
use App\Http\Requests\v1\NearByHuntsRequest;
use App\Http\Requests\v1\ParticipateRequest;
use App\Models\v1\Game;
use App\Models\v1\Hunt;
use App\Models\v1\Hunt as HuntV2;
use App\Models\v1\HuntComplexity;
use App\Models\v1\HuntUser;
use App\Models\v1\HuntUserDetail;
use App\Models\v2\HuntUser as HuntUserV2;
use App\Models\v2\HuntUserDetail as HuntUserDetailV2;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Validator;

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
    								->whereIn('city',['Lloydminster','Camrose','Fort McMurry','Beaumont','Cold Lake','Brooks','Lacombe'])
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
                                    ->where('identifier','!=','word_search')
                                    ->get()
                                    ->random(1);
                        
                        $rand_variation = rand(0,$game[0]['game_variation']->count()-1);
                        $gameVariationId = $game[0]['game_variation'][$rand_variation]->id;

                        $location['Type'] = 'Point';
                        $location['coordinates'] = [
                                                    $latlng[1],
                                                    $latlng[0]
                                                ];
                        
                        $target = ComplexityTarget::where([
                                    'game_id' => $game[0]->id, 
                                    'complexity'=> $key
                                ])
                                ->pluck('target')
                                ->first();
                        $huntComplexities->hunt_clues()->updateOrCreate([
                                            'hunt_complexity_id' =>  $huntComplexities->_id,
                                            'location.coordinates.0' =>  $latlng[0],
                                            'location.coordinates.1' =>  $latlng[1],
                                        ],[
                                            'hunt_complexity_id' => $huntComplexities->_id,
                                            'location'           => $location,
                                            'game_id'            => $game[0]->id,
                                            'game_variation_id'  => $gameVariationId,
                                            'target'  => $target
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $clue = (int)$request->get('star');
        $user = Auth::User();
        $userId = $user->id;
        $location = Hunt::select('location','name','place_name')
                                    ->whereHas('hunt_complexities', function ($query) use ($clue,$userId) {
                                        $query->where('complexity',$clue);
                                    })
                                    // ->with(['hunt_complexities'=>function($query) use ($clue,$userId){
                                    //     $query->where('complexity',(int)$clue)
                                    //     ->with(['hunt_users'=>function($hunt) use ($userId){
                                    //         $hunt->where('user_id',$userId);
                                    //     }]);
                                    // }])
                                    ->with(['hunt_users'=>function($query) use ($clue,$userId){
                                        $query->where('user_id',$userId)->where('complexity',$clue);
                                    }])
                                    ->get()
                                    ->map(function($query){
                                    	$location = $query->location['coordinates'];
                                    	$query->latitude = $location[1];
                                    	$query->longitude = $location[0];
                                        $query->name = ($query->name != "")?$query->name:$query->place_name;
                                        
                                        if (count($query->hunt_users)>0 && ($query->hunt_users[0]->status == 'participated' || $query->hunt_users[0]->status == 'progress')) {
                                            $query->already_participated = 1;
                                            $query->hunt_user_id = $query->hunt_users[0]->id;
                                        } else {
                                            $query->already_participated = 0;
                                            $query->hunt_user_id = "";
                                        }
                                        unset($query->location,$query->hunt_users);
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
            return response()->json(['message'=>$validator->messages()->first()],422);
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
            return response()->json(['message'=>$validator->messages()->first()],422);
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
                            ->select('hunt_id','complexity','est_completion','distance')
                            ->with('hunt_clues:_id,hunt_complexity_id,location,game_id,game_variation_id');
                    }])
                    ->where('_id',$huntId)
                    ->first();
        
        
        if (!$hunt) {
           return response()->json(['message'=>'Hunt details not found'],422);
        }
        $huntClues = [];
        $location = $hunt->location['coordinates'];
        $est_completion = '00:00:00';
        if (count($hunt->hunt_complexities) > 0) {
            foreach ($hunt->hunt_complexities[0]->hunt_clues as $key => $value) {
                $huntClues[] = [$value->location['coordinates'][0],$value->location['coordinates'][1]];
            }
            $est_completion = ($hunt->hunt_complexities[0]->est_completion != "")?$hunt->hunt_complexities[0]->est_completion:0;
        }
        

        $bestTime = 0;
        
        if (count($huntUser->pluck('finished_in')) > 0 && min($huntUser->pluck('finished_in')->toArray()) != 0) {
            $bestTime = min(array_diff($huntUser->pluck('finished_in')->toArray(), array(0)));
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
                    'distance'      => number_format($hunt->hunt_complexities[0]->distance/1000,1),
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::User();

        
        $data = $request->all();
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');
        $huntMode = $request->get('hunt_mode');
        $huntComplexitie = HuntComplexity::with('hunt_clues')
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

        $huntUserQuery = HuntUser::select('user_id','hunt_id','hunt_complexity_id','status','hunt_mode','skeleton','ended_at') 
                                    ->where([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                        //'status'             => 'completed',
                                    ])
                                    // ->whereIn('status',['participated','progress'])
                            ->latest()
                            ->first();
        if($huntUserQuery){
            if($huntUserQuery->status == 'participated' || $huntUserQuery->status == 'progress'){
                return response()->json([
                                    'message'=>'You already participated in this hunt.',
                                ],422);
            }

            $endedDate = $huntUserQuery->ended_at->addDays(1);
            if (Carbon::now() < $endedDate) {
                return response()->json([
                                    'message'=>'You have to wait 24 hours after completion of the hunt',
                                ],422);
            }     
        }


        if ($huntMode == 'challenge') {
            if ($user->gold_balance < $huntComplexitie->hunt->fees) {
                return response()->json([
                            'message'=>"you don't have enough balance",
                        ],422);
            }
            $coin = $user->gold_balance - $huntComplexitie->hunt->fees;
            $user->gold_balance = (int)$coin;
            $user->save();            
        }
        $skeleton = [];
        $huntUser = HuntUser::create([
                                        'user_id'            => $user->_id,
                                        'hunt_id'            => $huntId,
                                        'hunt_complexity_id' => $huntComplexitie->id,
                                        'valid'              => false,
                                        'status'             => 'participated',
                                        'hunt_mode'          => $huntMode,
                                        'started_at'         => null,
                                        'ended_at'           => null,
                                        'est_completion'     => $huntComplexitie->est_completion,
                                        'complexity'         => $star
                                    ]);

        
        foreach ($huntComplexitie->hunt_clues as $key => $value) {
            $huntUserDetail = HuntUserDetail::create([
                                                        'hunt_user_id'      => $huntUser->id,
                                                        'location'          => $value->location,
                                                        'game_id'           => $value->game_id,
                                                        'game_variation_id' => $value->game_variation_id,
                                                        'revealed_at'       => null,
                                                        'finished_in'       => 0,
                                                        'status'            => 'tobestart',
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

        $request->request->add(['hunt_user_id'=>$huntUser->id]);
        $data1 = (new HuntController)->getHuntParticipationDetails($request);
        
        return response()->json([
                            'message'=>'user has been successfully participants',
                            'remaining_coins' => $user->gold_balance,
                            'data'  => $data1->original['data'],
                        ]);
    
    }

    //GET HUNT USER
    public function getHuntUser(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::user();
        
        $data = $request->all();
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');

        $huntComplexitie = HuntComplexity::with('hunt_clues')
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::user();
        
        $data = $request->all();
        $huntUserId = $request->get('hunt_user_id');

        /** Pause the clue if running **/
        $runningClues = HuntUserDetail::where(['hunt_user_id' => $huntUserId, 'status' => 'running'])->get();

        foreach ($runningClues as $index => $clue) {
            $startdate = $clue->started_at;
            $endedDate = new MongoDBDate();
            $finishedIn = Carbon::now()->diffInMinutes($startdate);
            if ($clue->finished_in > 0) {
                $finishedIn += $clue->finished_in;
            }
            $clue->finished_in = (int)$finishedIn;
            $clue->started_at = new MongoDBDate();
            $clue->ended_at = null;
            $clue->status = 'pause';
            $clue->save();
        }

        $huntUser = HuntUser::select('_id','user_id','hunt_id','hunt_complexity_id','status','skeleton','hunt_mode','complexity')
                            ->where('_id',$huntUserId)
                            //->with('hunt_user_details:_id,hunt_user_id,location,est_completion,status')
                            ->with('hunt_complexities:_id,distance')
                            ->with(['hunt_user_details'=>function($query) use ($huntUserId){
                                $query->where('hunt_user_id',$huntUserId)
                                    ->select('_id','hunt_user_id','location','est_completion','status','game_id','game_variation_id','finished_in','started_at','ended_at')
                                    ->with('game:_id,name,identifier','game_variation');
                            }])
                            ->first();
       
        $skeleton_key_available = 0;
        $i = 1;
        foreach ($huntUser->skeleton as $key => $value) {
            if ($value['used'] == false) {
                $skeleton_key_available = $i;
                $i++;
            }    
        }
        
        if ($huntUser->hunt_complexities()->count() == 0) {
            return response()->json([
                                'message' => 'This hunt does not exist.',
                            ],422);
        }
        
        $distance = $huntUser->hunt_complexities->distance/1000;
        $huntUser->skeleton_key_available = $skeleton_key_available;
        $huntUser->time = array_sum($huntUser->hunt_user_details->pluck('finished_in')->toArray());
        $huntUser->total_clues = $huntUser->hunt_user_details->count();
        $huntUser->total_completed_clues = $huntUser->hunt_user_details->where('status','completed')->count();
        $huntUser->total_km = number_format($distance,2);
        $completedKm = ($distance/$huntUser->total_clues)*$huntUser->total_completed_clues;
        $huntUser->completed_km = number_format($completedKm,2);
        
        unset($huntUser->skeleton,$huntUser->hunt_complexities);
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }
        $star       = (int)$request->star;
        $page       = (int)$request->page;
        $user       = Auth::User();
        $userId     = $user->id;
        $longitude  = (float)$request->longitude;
        $latitude   = (float)$request->latitude;
        $page       = $page -1;
        $take       = 10;
        $skip       = ($page * $take);
        $distance   = 3000;
        // $latitude   = (float)$user->location['coordinates'][0];
        // $longitude  = (float)$user->location['coordinates'][1];
        $huntIds = HuntUser::where('user_id',$user->id)->pluck('hunt_id')->toArray();
        $hunts = Hunt::raw(function($collection) use ($latitude,$longitude,$distance,$star,$skip,$take,$huntIds,$userId)
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
                                            '$and' => [
                                                [ '_id' => ['$nin' => $huntIds] ]
                                            ]
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
                                    ['$unwind' => '$hunt_complexities'],
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
                                                               [ '$eq'=> [ '$user_id', $userId ] ],
                                                               [ '$eq'=> [ '$complexity', $star ] ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    '$project' => [
                                                        '_id' => [ '$toString' => '$_id' ],
                                                        'hunt_id' => true,
                                                        'user_id' => true,
                                                        'complexity' => true,
                                                        'status' => true,
                                                    ]
                                                ]
                                            ],
                                            'as' => 'hunt_participated_data'
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
                                            'place_name' => true,
                                            'location' => true,
                                            // 'distance' => true,
                                            'hunt_complexities' => true,
                                            'hunt_participated_data' => true,
                                            'already_participated' => [
                                                '$size' => '$hunt_participated_data'
                                            ],
                                        ]
                                    ]
                                ]);
                            });
    
        $hunts = $hunts->map(function($hunt, $key) use ($star){
            $hunt->latitude = $hunt->location->coordinates[1];
            $hunt->longitude = $hunt->location->coordinates[0];
            $hunt->hunt_user_id = (isset($hunt->hunt_participated_data[0]->_id))?$hunt->hunt_participated_data[0]->_id:"";
            $hunt->complexity = $star;
            unset($hunt->location);
            unset($hunt->hunt_complexities);
            unset($hunt->hunt_participated_data);
            return $hunt;
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
                                $query->whereIn('status',['tobestart','pause','progress']);
                            })
                            ->select('_id','user_id','hunt_id')
                            ->with(['hunt:_id,name,place_name,location'])
                            ->where([
                                        'user_id' => $user->_id,
                                    ])
                            ->get()
                            ->map(function($query){
                                $query->name = $query->hunt->name;
                                $query->place_name = $query->hunt->place_name;
                                $query->latitude = $query->hunt->location['coordinates'][1];
                                $query->longitude = $query->hunt->location['coordinates'][0];
                                $query->hunt_user_id = $query->_id;
                                unset($query->_id, $query->hunt , $query->user_id , $query->location);
                                return $query;
                            });

        return response()->json([
                                'message' => 'In progress hunt has been retrieved successfully',
                                'data'    => $huntUser
                            ]);
    }


    //previous Hunt Details
    public function getPreviousHuntDetails(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_id'=> "required|exists:hunt_users,_id",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::User();
        $id = $request->get('hunt_user_id');
        $huntUsers = HuntUser::with([
                                        'hunt:_id,name,place_name',
                                        'hunt_complexities:_id,complexity,distance',
                                        'hunt_user_details'
                                    ])
                               ->where([
                                        '_id'     => $id,
                                        'user_id' => $user->id,
                                    ])
                               ->first();
        $totalCompletedClues = $huntUsers->hunt_user_details->where('status','completed')->count();
        $time = $huntUsers->hunt_user_details->pluck('finished_in')->toArray();
        $totalClue = $huntUsers->hunt_user_details->count();
        $totalKm = $huntUsers->hunt_complexities->distance/1000;
        $completedKm = ($totalKm/$totalClue)*$totalCompletedClues;
        $data = [   
                    '_id'                   => $huntUsers->id,
                    'name'                  => $huntUsers->hunt->name,
                    'status'                => $huntUsers->status,
                    'complexity'            => $huntUsers->hunt_complexities->complexity,
                    'total_clues'           => $totalClue,
                    'total_completed_clues' => $totalCompletedClues,
                    'time'                  => array_sum($time),
                    'total_km'              => $totalKm,
                    'completed_km'          => $completedKm,
                ];

        
        return response()->json([
                                'message' => 'Previous hunt has been retrieved successfully',
                                'data'    => $data
                            ]);
    }






















    public function getHuntsByDifficultyV2(HuntByDiffRequest $request){

        $complexity = (int)$request->complexity;
        $user       = auth()->User();
        $userId     = $user->id;

        // $hunts = Hunt::select('_id', 'location', 'name', 'place_name')
                 //    ->whereHas('hunt_complexities', function ($query) use ($complexity,$userId) {
                 //     $query->where('complexity',$complexity);
                 //    })
                 //    ->with(['hunt_users'=> function($query) use ($complexity,$userId){
                 //     $query->where('user_id', $userId)
                 //     ->where('status', '!=','completed')
                 //     ->select('_id', 'user_id', 'hunt_id', 'hunt_mode', 'status', 'complexity');
                 //    }])
                 //    ->get();
        $hunts = Hunt::raw(function($collection) use ($userId, $complexity){
                    return $collection->aggregate([
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
                                                   [ '$eq'=> [ '$complexity', $complexity ] ]
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
                        ['$unwind' => '$hunt_complexities'],
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
                                                   [ '$eq'=> [ '$user_id', $userId ] ],
                                                   [ '$ne'=> [ '$status', 'completed' ] ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        '$project' => [
                                            '_id' => [ '$toString' => '$_id' ],
                                            'status' => true,
                                            'hunt_id' => true,
                                            'hunt_mode' => true,
                                            'complexity' => true,
                                            'user_id' => true,
                                        ]
                                    ]
                                ],
                                'as' => 'hunt_users'
                            ]
                        ],
                        [
                            '$project' => [
                                '_id' => true,
                                'name' => true,
                                'place_name' => true,
                                'location' => true,
                                'hunt_users' => true,
                            ]
                        ]
                    ]);
                });

        return response()->json(['message'=>'Hunt has been retrieved successfully','data'=>$hunts]);
    }

    public function getNearByHuntsV2(NearByHuntsRequest $request){

        $complexity = (int)$request->complexity;
        $page       = (int)$request->page;
        $user       = auth()->User();
        $userId     = $user->id;
        $longitude  = (float)$request->longitude;
        $latitude   = (float)$request->latitude;
        $page       = $page -1;
        $take       = 20;
        $skip       = ($page * $take);
        $distance   = 3000;
        
        $hunts = Hunt::raw(function($collection) use ($skip, $take, $userId, $complexity, $latitude, $longitude, $distance){
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
                                "limit"=> 1000,
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
                                                   [ '$eq'=> [ '$complexity', $complexity ] ]
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
                        ['$unwind' => '$hunt_complexities'],
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
                                                   [ '$eq'=> [ '$user_id', $userId ] ],
                                                   [ '$eq'=> [ '$complexity', $complexity ] ],
                                                   [ '$ne'=> [ '$status', 'completed' ] ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        '$project' => [
                                            '_id' => [ '$toString' => '$_id' ],
                                            'status' => true,
                                            'hunt_id' => true,
                                            'hunt_mode' => true,
                                            'complexity' => true,
                                            'user_id' => true,
                                        ]
                                    ]
                                ],
                                'as' => 'hunt_users'
                            ]
                        ],
                        [ '$sort' => [ 'distance' => 1 ] ],
                        ['$skip' => $skip],
                        ['$limit' => $take+1],
                        [
                            '$project' => [
                                '_id' => true,
                                'name' => true,
                                'place_name' => true,
                                'location' => true,
                                'hunt_users' => true,
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
    }

    public function getHuntParticipationDetailsV2(Request $request){
        
        $validator = Validator::make($request->all(),[
            'hunt_user_id'=> "required|exists:hunt_users,_id",
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = auth()->user();
        $huntUserId = $request->hunt_user_id;

        $huntUserDetails = HuntUserDetailV2::where(['hunt_user_id' => $huntUserId])->get();
        
        /** Pause the clue if running **/
        (new ClueController)->calculateTheTimer($huntUserDetails,'paused');
        return response()->json(['message' => 'Clues details of hunt in which user is participated, has been retrieved successfully.', 'data'=> $huntUserDetails]);
    }

    public function participateInHuntV2(ParticipateRequest $request){

        $user       = auth()->User();
        $complexity = (int)$request->complexity;
        $huntId     = $request->hunt_id;
        $huntMode   = $request->hunt_mode;

        $huntComplexity = HuntComplexity::with('hunt_clues')
                            ->with('hunt:_id,name,fees')
                            ->where(['complexity'=> $complexity, 'hunt_id'=> $huntId])
                            ->first();

        if(!$huntComplexity){
            return response()->json(['message'=> 'Hunt is not exist with the requested difficulty.'], 422);
        }

        $huntParticipation = $user->hunt_user_v1()->where(['hunt_id' => $huntId])->latest()->first();

        if ($huntParticipation && $huntParticipation->status == 'participated') {
            return response()->json(['message'=> 'You already participated in this hunt.'], 422);
        }

        if ($huntParticipation && $huntParticipation->status == 'completed') {
            
            $endedDate = $huntParticipation->ended_at->addDays(1);
            if ($endedDate > now()) {
                return response()->json(['message'=> 'You have to wait 24 hours after completion of the hunt'], 422);
            }
        }

        if ($huntMode == 'challenge') {
            
            if ($user->gold_balance < $huntComplexity->hunt->fees) {
                return response()->json([ 'message'=>"you don't have enough balance"], 422);
            }
            $coin = $user->gold_balance - $huntComplexity->hunt->fees;
            $user->gold_balance = (float)$coin;
            $user->save();
        }

        $huntUser = $user->hunt_user_v1()->create([
                        'hunt_id'    => $huntId,
                        'hunt_mode'  => $huntMode,
                        'complexity' => $complexity
                    ]);
        $skeleton        = [];
        $huntUserDetails = [];
        foreach ($huntComplexity->hunt_clues as $clue) {
            $huntUserDetails[] = new HuntUserDetailV2([
                'location'          => $clue->location,
                'game_id'           => $clue->game_id,
                'game_variation_id' => $clue->game_variation_id,
            ]);
            
            $skeleton[] = [
                'key'       => strtoupper(substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 10)), 0, 10)),
                'used'      => false ,
                'used_date' => null
            ];
        }
        $huntUser->skeleton = $skeleton;
        $huntUser->save();
        $huntUser->hunt_user_details()->saveMany($huntUserDetails);

        $request->request->add(['hunt_user_id'=>$huntUser->id]);
        $participationDetailData = (new HuntController)->getHuntParticipationDetailsV2($request);
        return response()->json([
            'message'=>'user has been successfully participants',
            'remaining_coins' => $user->gold_balance,
            'data'  => $participationDetailData->original['data'],
        ]);
    }

    public function getHuntDetailsV2(HuntDetailRequest $request){
        
        $user   = auth()->user();
        $userId = $user->id;
        $huntId     = $request->hunt_id;
        $complexity = (int)$request->complexity;

        $hunt = HuntV2::where('_id', $huntId)->select('_id','name','location','fees')->first();

        $huntDetails = $hunt->hunt_complexities()
                            ->where('complexity',$complexity)
                            ->select('hunt_id', 'complexity','est_completion','distance')
                            ->first();

        $totalClues = $huntDetails->hunt_clues()->count();

        $bestTime = $hunt->hunt_users()->pluck('finished_in')->min();
        
        $participated   = false;
        $completedClues = 0;
        $timeTaken      = 0;
        $completedDist  = 0;
        $userParticipation = $hunt->hunt_users()->where(['user_id'=> $userId, 'complexity'=> $complexity])->select()->first();
        if ($userParticipation) {
            $userClueDetails = $userParticipation->hunt_user_details;
            $participated = true;
            $completedClues = $userClueDetails->where('status','completed')->count();
            $timeTaken = $userClueDetails->sum('finished_in');
            $completedDist = (($huntDetails->distance / $totalClues) * $completedClues);
        }

        $hunt->complexity       = $huntDetails->complexity;           
        $hunt->est_completion   = $huntDetails->est_completion; /** Time in seconds **/   
        $hunt->total_dist       = $huntDetails->distance;   /** Distance in meters **/
        $hunt->total_clues      = $totalClues;           
        $hunt->best_time        = ($bestTime !== null)?$bestTime:0; /** Time in meters **/
        
        $hunt->participated     = $participated;           
        $hunt->completed_clues  = $completedClues;           
        $hunt->time_taken       = $timeTaken;           
        $hunt->completed_dist   = $completedDist;           

        return response()->json(['message'=>'Hunt details has been retrieved successfully.','data'=>$hunt]);
    }

    public function getHuntsInProgressV2(Request $request){

        $user = auth()->user();
        $userId = $user->id;

        $hunts = HuntV2::whereHas('hunt_users',function($query) use ($userId){
                                $query->where('user_id', $userId)
                                    ->whereHas('hunt_user_details',function($query){
                                        $query->whereIn('status',['tobestart','pause','progress']);
                                    });
                            })
                            ->with(['hunt_users' => function($query) use ($userId){
                                $query->where('user_id', $userId)->select('_id','status','hunt_id','hunt_mode','complexity','user_id');
                            }])
                            ->select('_id', 'name', 'place_name', 'location')
                            ->get();

        // $hunts = HuntUserV2::whereHas('hunt_user_details',function($query){
        //                         $query->whereIn('status',['tobestart','pause','progress']);
        //                     })
        //                     ->select('_id','user_id','hunt_id')
        //                     ->with(['hunt:_id,name,place_name,location'])
        //                     ->where([
        //                                 'user_id' => $user->_id,
        //                             ])
        //                     ->get();

        return response()->json([
                                'message' => 'In progress hunt has been retrieved successfully',
                                'data'    => $hunts
                            ]);
    }





























//     public function getTheHunts($conditions = [], $additionalData)
//     {
//         $mainCondition = $conditions['main_condition'];
//         $sortCondition = $conditions['sort_condition'];
//         $skipCondition = ($conditions['limit_condition'][0] < 0)?[]:$conditions['limit_condition'][0];
//         $limitCondition = ($conditions['limit_condition'][1] < 0)?[]:$conditions['limit_condition'][1];
// dd($conditions['limit_condition'][0]);
//         $userId     = $additionalData['user_id'];
//         $complexity = $additionalData['complexity'];

//         $hunts = Hunt::raw(function($collection) use ($mainCondition,$sortCondition ,$skipCondition, $limitCondition, $userId, $complexity){
//                     return $collection->aggregate([
//                         $mainCondition,
//                         [
//                             '$addFields' => [
//                                 'hunt_string_id' => [
//                                     '$toString' => '$_id'
//                                 ]
//                             ]
//                         ],
//                         [
//                             '$lookup' => [
//                                 'from' => 'hunt_complexities',
//                                 'let'=> [ 'hunt_string_id'=> '$hunt_string_id'],
//                                 'pipeline'=> [
//                                     [
//                                         '$match'=> [ 
//                                             '$expr'=> [ 
//                                                 '$and'=> [
//                                                    [ '$eq'=> [ '$hunt_id',  '$$hunt_string_id' ] ],
//                                                    [ '$eq'=> [ '$complexity', $complexity ] ]
//                                                 ]
//                                             ]
//                                         ]
//                                     ],
//                                     [
//                                         '$project' => [
//                                             '_id' => [ '$toString' => '$_id' ],
//                                             'hunt_id' => true,
//                                             'complexity' => true,
//                                         ]
//                                     ]
//                                 ],
//                                 'as' => 'hunt_complexities'
//                             ]
//                         ],
//                         ['$unwind' => '$hunt_complexities'],
//                         [
//                             '$lookup' => [
//                                 'from' => 'hunt_users',
//                                 'let'=> [ 'hunt_string_id'=> '$hunt_string_id'],
//                                 'pipeline'=> [
//                                     [
//                                         '$match'=> [ 
//                                             '$expr'=> [ 
//                                                 '$and'=> [
//                                                    [ '$eq'=> [ '$hunt_id',  '$$hunt_string_id' ] ],
//                                                    [ '$eq'=> [ '$user_id', $userId ] ],
//                                                    [ '$eq'=> [ '$complexity', $complexity ] ],
//                                                    [ '$ne'=> [ '$status', 'completed' ] ]
//                                                 ]
//                                             ]
//                                         ]
//                                     ],
//                                     [
//                                         '$project' => [
//                                             '_id' => [ '$toString' => '$_id' ],
//                                             'status' => true,
//                                             'hunt_id' => true,
//                                             'hunt_mode' => true,
//                                             'complexity' => true,
//                                             'user_id' => true,
//                                         ]
//                                     ]
//                                 ],
//                                 'as' => 'hunt_users'
//                             ]
//                         ],
//                         $sortCondition,
//                         $skipCondition,
//                         $limitCondition,
//                         [
//                             '$project' => [
//                                 '_id' => true,
//                                 'name' => true,
//                                 'place_name' => true,
//                                 'location' => true,
//                                 // 'hunt_complexities' = true,
//                                 // 'hunt_users' = true,
//                                 'already_participated' => [
//                                     '$size' => '$hunt_users'
//                                 ],
//                             ]
//                         ]
//                     ]);
//                 });
//             return $hunts;
//     }
}
