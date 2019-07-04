<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ActionOnClueRequest;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime as MongoDBDate;

class ClueController extends Controller
{
    
    public function __construct()
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set( 'serialize_precision', -1 );
        }
    }


    public function revealTheClue(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;
        
        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->revealed_at != null) {
            return response()->json(['message'=>'You cannot reveal this clue, as it already revealed.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntUserDetail->hunt_user()->update(['status'=> 'running']);
            
            $huntUserDetail->revealed_at = now();
            $huntUserDetail->started_at = now();
            $huntUserDetail->status = 'running';
            $huntUserDetail->save();
            return response()->json(['message'=>'Clue has been revealed successfully. now you can play the game.']);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }
    }

    public function startTheClue(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->user();
        $userId = $user->id;
        
        $huntUserDetail = $this->checkParticipationFromClue($userId, $huntUserDetailId);

        if ($huntUserDetail->status == 'running') {
            return response()->json(['message'=>'You cannot start this clue, as it already started.'], 422);
        }

        if ($huntUserDetail) {
            
            $huntUserDetail->hunt_user()->update(['status'=> 'running']);

            $huntUserDetail->started_at = new MongoDBDate();
            $huntUserDetail->status = 'running';
            $huntUserDetail->save();
            return response()->json(['message'=>'Clue Timer has been started successfully.']);
        }else{
            return response()->json(['message'=>'You are not participated in the hunt, you requested.'], 422);
        }
    }

    public function pauseTheClue(ActionOnClueRequest $request){

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

    public function endTheClue(ActionOnClueRequest $request){

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

    public function useTheSkeletonKey(ActionOnClueRequest $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->User();
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

        $huntUserDetail = HuntUserDetail::where('_id',$huntUserDetailId)
                            ->whereHas('hunt_user', function($query) use ($userId){
                                $query->where('user_id', $userId);
                            })
                            ->first();

        return $huntUserDetail;
    }

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
            $huntUserDetail->hunt_user()->update([ 'status'=>'completed', 'ended_at'=> new MongoDBDate(), 'finished_in'=> $huntUserDetail->sum('finished_in')]);
            // HuntUser::where([ '_id'=>$huntUserDetail->hunt_user_id, 'user_id'=>$user->id])
            // ->update([ 'status'=>'completed', 'ended_at'=> new MongoDBDate(), 'finished_in'=> $huntUserDetail->sum('finished_in')]);
            return true;
        }
        return false;
    }
}
