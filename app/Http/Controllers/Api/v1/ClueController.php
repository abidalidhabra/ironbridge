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

class ClueController extends Controller
{
    public function __construct()
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set( 'serialize_precision', -1 );
        }
    }

    //CLUE complete
    public function revealTheClue(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id",
                        // 'time'=> "required|integer",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();
        $id = $request->get('hunt_user_details_id');
        $data = [
                    'revealed_at' => new MongoDBDate(),
                    // 'finished_in' => (int)$request->get('time'),
                    'status'      => 'completed',
                    'started_at'  => new MongoDBDate(),
                ];

        $huntUserDetail = HuntUserDetail::where('_id',$id)->first();
        $huntUserDetail->update($data);

        if ($huntUserDetail) {
            $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                            ->whereIn('status',['progress','pause'])
                            ->count();
            
            if ($clueDetail == 0) {
                HuntUser::where([
                                    '_id'=>$huntUserDetail->hunt_user_id,
                                    'user_id'=>$user->id,
                                ])
                        ->update([
                                    'status'=>'completed',
                                    'ended_at'=> new MongoDBDate()
                                ]);
            }

            $huntUserDetail_complate = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                                                        ->where('status','completed')
                                                        ->count();
            
            if($huntUserDetail_complate  == 1){
                HuntUser::where([
                                '_id'=>$huntUserDetail->hunt_user_id,
                                'user_id'=>$user->id,
                            ])
                    ->update([
                                'status'=>'completed',
                                'started_at'=> new MongoDBDate()
                            ]);       
            }

        }
        return response()->json([
                                'message'=>'Revealed updated successfully '
                            ]);
    }

    //CLUE INFO
    public function userHuntInfo(Request $request){
        $user = Auth::User();        
        $huntUser = HuntUser::with([
                                    'hunt_user_details:_id,hunt_user_id,revealed_at,finished_in,status',
                                    'hunt:_id,name,place_name'
                                ])
                            ->where('user_id',$user->id)
                            ->first();

        $clue_complete = $huntUser->hunt_user_details->pluck('revealed_at')->filter()->count();
        $data = [
                    'hunt_name' => ($huntUser->hunt->name != "")?$huntUser->hunt->name:$huntUser->hunt->place_name,
                    'clues' => $clue_complete.' Of '.$huntUser->hunt_user_details->count(),
                    'distance' => 0,
                    'current_time' => '00:00:00',
                ];
        return response()->json([
                                'message' => 'user has been retrieved successfully',
                                'data'    => $data
                            ]);
    }


    //CLUE BASED GAME DETAILS
    public function clueGame(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();
        $huntUserDetailId = $request->get('hunt_user_details_id');
        
        $huntUser = HuntUser::with(['hunt_user_details'=>function($query) use ($huntUserDetailId){
                                $query->where('_id',$huntUserDetailId)
                                    ->with('game:_id,name,identifier','game_variation:_id,variation_name');
                            }])
                            ->where('user_id',$user->id)
                            ->first();


        $data = [
                    'game' => $huntUser->hunt_user_details[0]->game,
                    'game_variation' => $huntUser->hunt_user_details[0]->game_variation,
                ];

        return response()->json([
                                'message' => 'Clue game has been retrieved successfully',
                                'data'    => $data
                            ]);
    }

    //REMOVE PARTICIPET
    public function quitTheHunt(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_id'=> "required|exists:hunt_users,_id",
                        //'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }
        $data = $request->all();
        /*$star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');*/
        $user = Auth::User();
        /*$huntComplexitie = HuntComplexitie::with('hunt_users')
                                            ->where([
                                                        'complexity' => $star,
                                                        'hunt_id'    => $huntId,
                                                    ])
                                            ->first();*/
        $id = $request->get('hunt_user_id');
        $huntUser = HuntUser::where('_id',$id)
                            ->where('user_id',$user->id)
                            ->first();
        
        if ($huntUser) {
            $huntUser->hunt_user_details()->delete();
            $huntUser->delete();
        }
        return response()->json([
                                'message' => 'Clue has been successfully delete'
                            ]);
    }

    //CLUE PAUSE
    public function cluePause(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id"
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))
                                        ->first();
        $huntUserDetail->update(['status'=>'pause']);

        if ($huntUserDetail) {
            $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                            ->where('status','pause')
                            ->count();

            if ($clueDetail == 1) {
                $data = [
                    'started_at' => new MongoDBDate(),
                ];                
                $huntUserDetail->update($data);
            }

        }
        return response()->json([
                                'message' => 'Clue pause has been updated successfully',
                            ]);
    }


    //SKELETON
    public function skeleton_old(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'=> "required|exists:hunts,_id",
                        'star'=> "required",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }
        $user = Auth::User();
        $star = (int)$request->get('star');
        $huntId = $request->get('hunt_id');
        $huntComplexitie = HuntComplexitie::with('hunt_users')
                                            ->where([
                                                        'complexity' => $star,
                                                        'hunt_id'    => $huntId,
                                                    ])
                                            ->first();
        $huntUser = HuntUser::where('hunt_complexity_id',$huntComplexitie->id)
                            ->where('user_id',$user->id)
                            ->where('skeleton.used',false)
                            ->first();

        $skeletonKey = "";
        if ($huntUser) {
            foreach ($huntUser->skeleton as $key => $value) {
                if ($value['used'] == false) {
                    $skeletonKey = $value['key'];
                    break;
                }
            }
            HuntUser::where('hunt_complexity_id',$huntComplexitie->id)
                                ->where('user_id',$user->id)
                                ->where('skeleton.key',$skeletonKey)
                                ->update(['skeleton.$.used'=>true , 'skeleton.$.used_date'=>new MongoDBDate()]);

        }


        return response()->json([
                                'message' => 'Skeleton used has been successfully'
                            ]);
    }

    public function skeleton(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id"
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }


        $user = Auth::User();
        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))->first();


        $huntUser = HuntUser::where('_id',$huntUserDetail->hunt_user_id)
                            ->where('user_id',$user->id)
                            ->where('skeleton.used',false)
                            ->first();


        $skeletonKey = "";
        if ($huntUser) {
            foreach ($huntUser->skeleton as $key => $value) {
                if ($value['used'] == false) {
                    $skeletonKey = $value['key'];
                    break;
                }
            }
            HuntUser::where('user_id',$user->id)
                    ->where('_id',$huntUserDetail->hunt_user_id)
                    ->where('skeleton.key',$skeletonKey)
                    ->update(['skeleton.$.used'=>true , 'skeleton.$.used_date'=>new MongoDBDate()]);

            $startdate = $huntUserDetail->started_at;
            $huntUserDetail->ended_at = new MongoDBDate();
            $huntUserDetail->huntUserDetail = Carbon::now()->diffInSeconds($startdate);
            $huntUserDetail->save();

            if ($huntUserDetail) {
                $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                                ->whereIn('status',['progress','pause'])
                                ->count();
                if ($clueDetail == 0) {
                    HuntUser::where([
                                        '_id'=>$huntUserDetail->hunt_user_id,
                                        'user_id'=>$user->id,
                                    ])
                            ->update([
                                        'status'=>'completed',
                                        'ended_at'=> new MongoDBDate()
                                    ]);
                }
            }

        }


        return response()->json([
                                'message' => 'Skeleton used has been successfully'
                            ]);

    }

    //GAME FINISH
    public function endTheClue(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id"
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();
        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))->first();
        $startdate = $huntUserDetail->started_at;
        $huntUserDetail->ended_at = new MongoDBDate();
        $huntUserDetail->huntUserDetail = Carbon::now()->diffInSeconds($startdate);
        $huntUserDetail->save();
        
        if ($huntUserDetail) {
            $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                            ->whereIn('status',['progress','pause'])
                            ->count();
            if ($clueDetail == 0) {
                HuntUser::where([
                                    '_id'=>$huntUserDetail->hunt_user_id,
                                    'user_id'=>$user->id,
                                ])
                        ->update([
                                    'status'=>'completed',
                                    'ended_at'=> new MongoDBDate()
                                ]);
            }
        }
        return response()->json([
                                'message'=>'Game is completed'
                            ]);        
    }
}
