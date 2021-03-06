<?php

namespace App\Http\Controllers\Api\v2;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hunt\UseTheSkeletonKeyRequest;
use App\Http\Requests\v1\ActionOnClueRequest;
use App\Models\v1\User;
use App\Models\v1\WidgetItem;
use App\Models\v2\HuntReward;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Models\v2\PracticeGameUser;
use App\Repositories\Hunt\Factory\ClueFactory;
use App\Repositories\Hunt\HuntUserDetailRepository;
use Exception;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use stdClass;

class ClueController extends Controller
{
    
    public function __construct()
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set( 'serialize_precision', -1 );
        }
    }

    public function actionOnClue(ActionOnClueRequest $request){

        try {
            $initializeAction = (new ClueFactory)->initializeAction($request->status);
            $data = $initializeAction->action($request);
            $rewardData = $data['rewardData'] ?? null;
            $finishedIn = $data['finishedIn'] ?? 0;
            return response()->json(['message'=>'Action on clue has been taken successfully.', 'data'=> $rewardData, 'finished_in'=> $finishedIn]);
        } catch (ChestBucketCapacityOverflowException $e) {
            return response()->json(['message' => $e->getMessage()], 422); 
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

     public function useTheSkeletonKey(UseTheSkeletonKeyRequest $request){

        try {

            // get parameters
            $huntUserDetailId = $request->hunt_user_details_id;
            $userId = auth()->user()->id;

            // reduce the skeleton key by one and get back the fresh user data
            User::where(['skeleton_keys.used_at' => null, '_id'=> $userId])->update(['skeleton_keys.$.used_at'=> new MongoDBDate()]);
            $freshUser = User::where('_id', $userId)->select('_id', 'skeleton_keys')->first();

            // mark the clue as complete
            $actionOnClueRequest = new ActionOnClueRequest();
            $actionOnClueRequest->setMethod('POST');
            $actionOnClueRequest->request->add(['hunt_user_details_id'=> $huntUserDetailId, 'status'=> 'completed']);
            $huntFinished = (new ClueController)->actionOnClue($actionOnClueRequest);

            // get hunt user detail and set the skip flag
            $huntUserDetail = (new HuntUserDetailRepository)->find($huntUserDetailId);
            $huntUserDetail->skipped_at = now();
            $huntUserDetail->save();

            // return the response to client
            return response()->json(['message'=> 'Clue Timer has been ended successfully.', 'data'=> $huntFinished->original['data'], 'available_skeleton_keys'=> $freshUser->available_skeleton_keys]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function markTheMiniGameAsFail(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(),[
                'hunt_user_details_id'=> "required|exists:hunt_user_details,_id",
            ]);

            if ($validator->fails()) {
                return response()->json(['message'=>$validator->messages()->first()],422);
            }

            (new HuntUserDetailRepository)->push(['_id'=> $request->hunt_user_details_id], 'failures_at', [ new MongoDBDate() ]);
            return response()->json(['message'=> 'MiniGame marked as fail.']);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }
    // public function actionOnClue(ActionOnClueRequest $request){

    //     $userId = auth()->user()->id;
    //     $huntUserDetailId = $request->hunt_user_details_id;
    //     $status = $request->status;
        
    //     $huntUserDetail = $this->getHuntUserDetail($userId, $huntUserDetailId);
        
    //     $stillRemain;
    //     $finishedIn = 0;
    //     switch ($status) {
            
    //         case 'reveal':
    //             $huntAction = 'running';
    //             $huntUserDetail->revealed_at = now();
    //             $huntUserDetail->started_at = now();
    //             $huntUserDetail->status = 'running';

    //             $huntUserDetails = $huntUserDetail->hunt_user->hunt_user_details()->get();
    //             if ($huntUserDetails->count() == $huntUserDetails->where('revealed_at', null)->count()) {
    //                 $this->takeActionOnHuntUser($huntUserDetail, '', [ 'started_at'=> now() ]);
    //             }
    //             break;
            
    //         case 'running':
    //             $huntAction = 'running';
    //             // $huntUserDetail->started_at = new MongoDBDate();
    //             $huntUserDetail->started_at = now();
    //             $huntUserDetail->status = 'running';
    //             break;

    //         case 'paused':
    //             $huntAction = 'paused';
    //             $this->calculateTheTimer($huntUserDetail,'paused');
    //             break;

    //         case 'completed':
    //             // $finishedIn = $this->calculateTheTimer($huntUserDetail,'completed');
    //             $this->calculateTheTimer($huntUserDetail,'completed');
    //             $this->unlockeMiniGameIfLocked($huntUserDetail->game_id, $userId);
    //             // $stillRemain = $huntUserDetail->hunt_user->hunt_user_details()->whereIn('status', ['tobestart','progress','pause'])->count();
    //             // $stillRemain = $huntUserDetail->hunt_user->hunt_user_details()->where('status', '!=', 'completed')->count();
    //             $huntUserDetails = $huntUserDetail->hunt_user->hunt_user_details()->get();
    //             $stillRemain = $huntUserDetails->where('status', '!=', 'completed')->count();
    //             $finishedIn = $huntUserDetails->sum('finished_in');
    //             break;
    //     }
    //     $huntUserDetail->save();
        
    //     $gameData = null;
    //     if ($status == 'completed' && $stillRemain == 0) {
    //         $fields = [ 'status'=>'completed', 'ended_at'=> now(), 'finished_in'=> $finishedIn ];
    //         $gameData = $this->takeActionOnHuntUser($huntUserDetail, '', $fields, true);
    //     }else if($status != 'completed'){
    //         $this->takeActionOnHuntUser($huntUserDetail, $huntAction);
    //     }

    //     return response()->json(['message'=>'Action on clue has been taken successfully.', 'hunt_info'=> $gameData, 'finished_in'=> $finishedIn]);
    // }

    // public function useTheSkeletonKey(Request $request){
        
    //     try {
            
    //         $huntUserDetailId = $request->hunt_user_details_id;
    //         $user   = auth()->User();
    //         $userId = $user->id;
            
    //         // for ($i=0; $i < 1000; $i++) { 
    //         //     $user->push('skeleton_keys',['key'=> new MongoDBId(), 'created_at'=> new MongoDBDate()]);
    //         // }
    //         // exit;
    //         $huntUserDetail = $this->getHuntUserDetail($userId, $huntUserDetailId);

    //         if (!$huntUserDetail) {
    //             return response()->json(['message'=>'Invalid hunt user detail id provided.'], 500);
    //         }

    //         if ($huntUserDetail->status == 'completed') {
    //             return response()->json(['message'=>'You cannot use skeleton key in this clue, as it already ended.'], 422);
    //         }

    //         // $skeletonExists = User::where(['skeleton_keys.used_at' => null, '_id'=> $userId])->project(['_id'=> true, 'skeleton_keys.$'=>true])->first();
    //         $skeletonExists = User::where(['skeleton_keys.used_at' => null, '_id'=> $userId])->update(['skeleton_keys.$.used_at'=> new MongoDBDate()]);
    //         $freshUser = User::where('_id', $userId)->first();
    //         if (!$skeletonExists) {
    //             return response()->json(['message'=>'You do not have sufficient skeleton keys.'], 422);
    //         }

    //         // $huntFinished = $this->actionOnClue($huntUserDetail, 'completed');
    //         $actionOnClueRequest = new ActionOnClueRequest();
    //         $actionOnClueRequest->setMethod('POST');
    //         $actionOnClueRequest->request->add(['hunt_user_details_id'=> $huntUserDetailId, 'status'=> 'completed']);
    //         $huntFinished = (new ClueController)->actionOnClue($actionOnClueRequest);

    //         return response()->json(['message'=> 'Clue Timer has been ended successfully.', 'hunt_action'=> $huntFinished->original, 'available_skeleton_keys'=> $freshUser->available_skeleton_keys]);
           
    //     } catch (Exception $e) {
    //         return response()->json(['message'=> $e->getMessage()]);
    //     }
    // }

    public function getHuntUserDetail($userId, $huntUserDetailId){

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

    public function takeActionOnHuntUser($huntUserDetail, $action, $fields = [], $gameCompleted = false){
        
        if (count($fields) == 0) {
            $fields = ['status'=> $action];
        }
        $huntUser = $huntUserDetail->hunt_user()->update($fields);
        
        if($gameCompleted){
            return $this->generateReward($huntUserDetail);
        }else{
            return null;
        }
    }

    public function generateReward($huntUserDetail){

        /** Generate Reward **/
        $randNumber  = rand(1, 1000);
        // $randNumber  = 12;
        // $randNumber  = 750;
        // $randNumber  = 450;
        $huntUser    = $huntUserDetail->hunt_user()->select('complexity','user_id')->first();
        $complexity  = $huntUser->complexity;
        // $complexity  = 1;
        $user        = auth()->user();
        // $userId      = $huntUser->user_id;
        $userId      = $user->_id;
        // $userGender  = $user->gender?:'male';
        $userGender  = ($user->avatar_detail)?$user->avatar_detail->gender:'male';
        $rewards     = HuntReward::all();

        $selectedReward = $rewards->where('complexity',$complexity)->where('min_range', '<=', $randNumber)->where('max_range','>=',$randNumber)->first();
        if (!$selectedReward) {
            return [ 'reward_messages' => 'No reward found.', 'reward_data' => new stdClass()];
        }

        $rewardData['random_number'] = $randNumber;
        
        if ($selectedReward->widgets_order && is_array($selectedReward->widgets_order)) {
            
            $widgetOrder     = $selectedReward->widgets_order;
            
            findWidget:
            $countableWidget = $widgetOrder[0];
            $widgetCategory  = $countableWidget['type'];
            $widgetName      = $countableWidget['widget_name'];

            $userWidgets = collect($user->widgets)->pluck('id');
            $widgetItems = WidgetItem::when($widgetCategory != 'all', function ($query) use ($widgetCategory){
                                return $query->where('widget_category', $widgetCategory);
                            })
                            ->havingGender($userGender)
                            ->where('widget_name',$widgetName)
                            ->whereNotIn('_id',$userWidgets)
                            ->select('_id', 'widget_name', 'avatar_id', 'widget_category')
                            ->first();

            if (!$widgetItems) {
                $widgetOrder = array_splice($widgetOrder, 1);
                if (count($widgetOrder) == 0) { goto distSkeleton; }
                goto findWidget; 
            }

            // User::where('_id',$user->id)->where('widgets.id', '!=', $widgetItemId)->push(['widgets'=> ['id'=> $widgetItemId, 'selected'=> false]]);
            $widget = [
                'id'=> $widgetItems->id,
                'selected'=> false
            ];
            $user->push('widgets', $widget);
            $message[] = 'Widget has been unlocked';
            $rewardData['widget'] = $widgetItems;
        }

        if ($selectedReward->skeletons){
            distSkeleton:
            $skeletons = [];
            for ($i=0; $i < $selectedReward->skeletons; $i++) { 
                $skeletons[] = [
                    'key'       => strtoupper(substr(uniqid(), 0, 10)),
                    'created_at'=> new MongoDBDate() ,
                    'used_at'   => null
                ];
            }
            $user->push('skeleton_keys', $skeletons);
            $message[] = 'Skeleton key provided';
            $rewardData['skeleton_keys'] = $skeletons;
        }

        if ($selectedReward->gold_value){
            distGold:
            $user->gold_balance += $selectedReward->gold_value;
            $user->save();
            $message[] = 'Gold provided.';
            $rewardData['golds'] = $selectedReward->gold_value;
        }

        unset($selectedReward->min_range, $selectedReward->max_range);
        \Log::info([ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData]);
        return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
    }

    public function unlockeMiniGameIfLocked($completedGameId, $userId)
    {
        return PracticeGameUser::where(['game_id'=> $completedGameId, 'user_id'=> $userId])->whereNull('unlocked_at')
                ->update(['unlocked_at'=> new MongoDBDate(now())]);
    }
}
