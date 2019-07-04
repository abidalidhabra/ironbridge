<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ActionOnClueRequest;
use App\Models\v1\Hunt;
use App\Models\v1\HuntComplexity;
use App\Models\v1\HuntUser;
use App\Models\v1\HuntUserDetail;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Validator;
use App\Models\v2\HuntUser as HuntUserV2;
use App\Models\v2\HuntUserDetail as HuntUserDetailV2;

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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::User();
        $id = $request->get('hunt_user_details_id');
        $data = [
                    'revealed_at' => new MongoDBDate(),
                    // 'finished_in' => (int)$request->get('time'),
                    'status'      => 'progress',
                ];

        $huntUserDetail = HuntUserDetail::where('_id',$id)->first();
        $huntUserDetail->update($data);

        if ($huntUserDetail) {
            // $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
            //                 ->whereIn('status',['progress','pause'])
            //                 ->count();
            
            // if ($clueDetail == 0) {
            //     HuntUser::where([
            //                         '_id'=>$huntUserDetail->hunt_user_id,
            //                         'user_id'=>$user->id,
            //                     ])
            //             ->update([
            //                         'status'=>'completed',
            //                         'ended_at'=> new MongoDBDate()
            //                     ]);
            // }

            // $huntUserDetail_complate = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
            //                                             ->where('status','completed')
            //                                             ->count();
            
                HuntUser::where([
                                '_id'=>$huntUserDetail->hunt_user_id,
                                'user_id'=>$user->id,
                            ])
                        ->update([
                            'status'=>'progress',
                            'started_at'=> new MongoDBDate()
                        ]);       
            

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
            return response()->json(['message'=>$validator->messages()->first()],422);
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
            return response()->json(['message'=>$validator->messages()->first()],422);
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))
        ->first();
        $startdate = $huntUserDetail->started_at;
        $huntUserDetail->ended_at = new MongoDBDate();

        $finishedIn = Carbon::now()->diffInMinutes($startdate);
        if ($huntUserDetail->finished_in > 0) {
            $finishedIn += $clue->finished_in;
        }
        $huntUserDetail->finished_in = (int)$finishedIn;
        $huntUserDetail->status = 'pause';
        $huntUserDetail->save();

        return response()->json([
            'message' => 'Clue pause has been updated successfully',
        ]);
    }

    public function startTheClue(Request $request){
      
        $validator = Validator::make($request->all(),[
            'hunt_user_details_id' => "required|exists:hunt_user_details,_id"
        ]);
       
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))->first();
        $huntUserDetail->started_at = new MongoDBDate();
        $huntUserDetail->ended_at = null;
        $huntUserDetail->status = 'progress';
        $huntUserDetail->save();
        
        return response()->json([
            'message' => 'Clue started successfully',
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
        $huntComplexitie = HuntComplexity::with('hunt_users')
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }


        $user = Auth::User();
        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))->first();


        $huntUser = HuntUser::where('_id',$huntUserDetail->hunt_user_id)
                            ->where('user_id',$user->id)
                            ->where('skeleton.used',false)
                            ->first();

        if (!$huntUser) {
            return response()->json([
                                'message' => 'Skeleton key not available'
                            ],422);
        }
        $skeletonKey = "";
        if ($huntUser) {
            foreach ($huntUser->skeleton as $key => $value) {
                if ($value['used'] == false) {
                    $skeletonKey = $value['key'];
                    
                    HuntUser::where('user_id',$user->id)
                            ->where('_id',$huntUserDetail->hunt_user_id)
                            ->where('skeleton.key',$skeletonKey)
                            ->update(['skeleton.$.used'=>true , 'skeleton.$.used_date'=>new MongoDBDate()]);
                    
                    $startdate = $huntUserDetail->started_at;
                    // $huntUserDetail->started_at = new MongoDBDate();
                    $huntUserDetail->ended_at = new MongoDBDate();
                    $huntUserDetail->finished_in = Carbon::now()->diffInMinutes($startdate);
                    // $huntUserDetail->finished_in = 0;
                    $huntUserDetail->save();

                    if ($huntUserDetail) {
                        $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                                        ->whereIn('status',['tobestart','pause'])
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
                    break;
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
            return response()->json(['message'=>$validator->messages()->first()],422);
        }

        $user = Auth::User();
        $huntUserDetail = HuntUserDetail::where('_id',$request->get('hunt_user_details_id'))->first();
        $startdate = $huntUserDetail->started_at;
        $huntUserDetail->ended_at = new MongoDBDate();
        $huntUserDetail->status = 'completed';
        $huntUserDetail->finished_in = Carbon::now()->diffInMinutes($startdate);
        $huntUserDetail->save();
        
        if ($huntUserDetail) {
            $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                            ->whereIn('status',['tobestart','progress','pause'])
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






























    public function revealTheClueV2(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;
        
        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->revealed_at != null) {
            return response()->json(['message'=>'You cannot reveal this clue, as it already revealed.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntUserDetail->revealed_at = now();
            $huntUserDetail->started_at = now();
            $huntUserDetail->status = 'running';
            $huntUserDetail->save();
            return response()->json(['message'=>'Clue has been revealed successfully. now you can play the game.']);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }
    }

    public function startTheClueV2(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;
        
        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->status == 'running') {
            return response()->json(['message'=>'You cannot start this clue, as it already started.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntUserDetail->started_at = new MongoDBDate();
            $huntUserDetail->status = 'running';
            $huntUserDetail->save();
            return response()->json(['message'=>'Clue Timer has been started successfully.']);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }
    }

    public function pauseTheClueV2(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;

        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->status == 'paused') {
            return response()->json(['message'=>'You cannot pause this clue, as it already paused.'], 422);
        }

        if ($huntUserDetail) {
            
            $this->calculateTheTimer($huntUserDetail,'paused');
            return response()->json(['message'=>'Clue Timer has been paused successfully.']);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }
    }

    public function endTheClueV2(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;

        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->status == 'completed') {
            return response()->json(['message'=>'You cannot end this clue, as it already ended.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntFinished = $this->markHuntAsComplete($huntUserDetail);
            return response()->json([
                'message'=> 'Clue Timer has been ended successfully.',
                'hunt_finished'=> $huntFinished
            ]);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }   
    }

    public function useTheSkeletonKeyV2(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = Auth::User();
        $userId = $user->id;
        
        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);


        if ($huntUserDetail->status == 'completed') {
            return response()->json(['message'=>'You cannot use skeleton key in this clue, as it already ended.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntUser = $huntUserDetail
                            ->hunt_user
                            ->where('user_id',$user->id)
                            ->where('skeleton.used',false)
                            ->first();

            if ($huntUser) {
                
                $huntUser->where('skeleton.used',false)
                        ->update([
                            'skeleton.$.used'=>true, 
                            'skeleton.$.used_date'=>new MongoDBDate()
                        ]);
            }else{
                return response()->json(['message'=>'You does not have any key to use.'], 422);
            }

            $huntFinished = $this->markHuntAsComplete($huntUserDetail);
            return response()->json([
                'message'=> 'Clue Timer has been ended successfully.',
                'hunt_finished'=> $huntFinished
            ]);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }   
    }
    
    public function checkParticipationFromClue($userId, $huntUserDetailId){

        $huntUserDetail = HuntUserDetailV2::where('_id',$huntUserDetailId)
                            ->whereHas('hunt_user', function($query) use ($userId){
                                $query->where('user_id', $userId);
                            })
                            ->first();

        return $huntUserDetail;
    }
    
    // public function calculateTheTimer($huntUserDetail, $action){

    //     $startdate  = $huntUserDetail->started_at;
    //     $finishedIn = $huntUserDetail->finished_in + now()->diffInSeconds($startdate);

    //     $huntUserDetail->finished_in = $finishedIn;
    //     $huntUserDetail->started_at  = null;
    //     $huntUserDetail->ended_at    = null;
    //     $huntUserDetail->status      = $action;
    //     $huntUserDetail->save();
    //     return true;
    // }

    public function calculateTheTimer($huntUserDetails, $action){

        if (is_a($huntUserDetails, 'Illuminate\Database\Eloquent\Collection')) {

            $runningClues = $huntUserDetails->where('status', 'running');
            $runningClues->map(function($clue) use ($action){
                $startdate  = $clue->started_at;
                $finishedIn = $clue->finished_in + now()->diffInSeconds($startdate);

                $clue->finished_in = $finishedIn;
                $clue->started_at  = null;
                $clue->ended_at    = null;
                $clue->status      = $action;
                $clue->save();
                return $clue;
            });
        }else{

            $startdate  = $huntUserDetails->started_at;
            $finishedIn = $huntUserDetails->finished_in + now()->diffInSeconds($startdate);

            $huntUserDetails->finished_in = $finishedIn;
            $huntUserDetails->started_at  = null;
            $huntUserDetails->ended_at    = null;
            $huntUserDetails->status      = $action;
            $huntUserDetails->save();
        }
        return true;
    }


    public function markHuntAsComplete($huntUserDetail){

        $this->calculateTheTimer($huntUserDetail, 'completed');

        $stillRemain = $huntUserDetail->whereIn('status', ['tobestart','progress','pause'])->count();
        if (!$stillRemain) {
            HuntUserV2::where([ '_id'=>$huntUserDetail->hunt_user_id, 'user_id'=>$user->id])
            ->update([ 'status'=>'completed', 'ended_at'=> new MongoDBDate(), 'finished_in'=> $huntUserDetail->sum('finished_in')]);
            return true;
        }
        return false;
    }
}
