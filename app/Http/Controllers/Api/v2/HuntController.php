<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Api\v2\ClueController;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\HuntByDiffRequest;
use App\Http\Requests\v1\HuntDetailRequest;
use App\Http\Requests\v1\NearByHuntsRequest;
use App\Http\Requests\v1\ParticipateRequest;
use App\Models\v2\Hunt;
use App\Models\v2\HuntComplexity;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HuntController extends Controller
{
    
    public function __construct(){
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set( 'serialize_precision', -1 );
        }
    }
    
    public function getHuntsByDifficulty(HuntByDiffRequest $request){

        $complexity = (int)$request->complexity;
        $user       = auth()->User();
        $userId     = $user->id;

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

        return response()->json(['message'=>'Hunts has been retrieved successfully','data'=>$hunts]);
    }

    public function getNearByHunts(NearByHuntsRequest $request){

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

    public function getHuntsInProgress(Request $request){

        $user = auth()->user();
        $userId = $user->id;

        $hunts = Hunt::whereHas('hunt_users',function($query) use ($userId){
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

        return response()->json(['message'=> 'In progress hunts has been retrieved successfully', 'data'=> $hunts]);
    }

    public function getHuntParticipationDetails(Request $request){
        
        $validator = Validator::make($request->all(),[
            'hunt_user_id'=> "required|exists:hunt_users,_id",
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = auth()->user();
        $huntUserId = $request->hunt_user_id;

        $huntUserDetails = HuntUserDetail::where(['hunt_user_id' => $huntUserId])
                            ->with('game:_id,name,identifier','game_variation:_id,variation_name,variation_complexity,target,no_of_balls,bubble_level_id,game_id')
                            ->select('_id','finished_in','status','location','game_id','game_variation_id','hunt_user_id')
                            ->get();
        
        $hunt = $huntUserDetails->first()->hunt_user()->select('_id', 'user_id', 'hunt_id', 'status')->first();

        /** Pause the clue if running **/
        (new ClueController)->takeActionOnClue($huntUserDetails,'paused');
        return response()->json([
            'message' => 'Clues details of hunt in which user is participated, has been retrieved successfully.', 
            'hunt'=> $hunt,
            'clues_data'=> $huntUserDetails
        ]);
    }

    public function participateInHunt(ParticipateRequest $request){

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
                        'complexity' => $complexity,
                        'hunt_complexity_id' => $huntComplexity->id
                    ]);
        $skeleton        = [];
        $huntUserDetails = [];
        foreach ($huntComplexity->hunt_clues as $clue) {
            $huntUserDetails[] = new HuntUserDetail([
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
        $huntUser->skeleton_keys = $skeleton;
        $huntUser->save();
        $huntUser->hunt_user_details()->saveMany($huntUserDetails);

        $request->request->add(['hunt_user_id'=>$huntUser->id]);
        $participationDetailData = (new HuntController)->getHuntParticipationDetails($request);
        return response()->json([
            'message'=>'user has been successfully participants',
            'remaining_coins' => $user->gold_balance,
            'data'  => $participationDetailData->original['data'],
        ]);
    }

    public function getHuntDetails(HuntDetailRequest $request){
        
        $user   = auth()->user();
        $userId = $user->id;
        $huntId     = $request->hunt_id;
        $complexity = (int)$request->complexity;

        $hunt = Hunt::where('_id', $huntId)->select('_id','name','location','fees', 'status')->first();

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
        $status         = "";
        $userParticipation = $hunt->hunt_users()->where(['user_id'=> $userId, 'complexity'=> $complexity])->select()->first();
        if ($userParticipation) {
            $userClueDetails = $userParticipation->hunt_user_details;
            $status = $userParticipation->status;
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
        $hunt->user_hunt_status = $status;           

        return response()->json(['message'=>'Hunt details has been retrieved successfully.','data'=>$hunt]);
    }

    public function pauseTheHunt(Request $request){
        
        $validator = Validator::make($request->all(),[
            'hunt_user_id'=> "required|exists:hunt_users,_id",
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = auth()->user();
        $huntUserId = $request->hunt_user_id;

        $huntUserDetails = HuntUser::where(['_id' => $huntUserId])->update(['status'=> 'paused']);

        return response()->json(['message'=>'Hunt has been marked as paused successfully.']);
    }

     //UPDATE LOCATION
    public function updateClues(Request $request){
        
        \Log::info($request->data);

        $validator = Validator::make($request->all(),[ 'data' => "required" ]);
        
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
}
