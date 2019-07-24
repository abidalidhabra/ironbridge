<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ActionOnClueRequest;
use App\Models\v1\WidgetItem;
use App\Models\v2\HuntReward;
// use App\Models\v1\User;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
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

        $userId = auth()->user()->id;
        $huntUserDetailId = $request->hunt_user_details_id;
        $status = $request->status;
        
        $huntUserDetail = $this->getHuntUserDetail($userId, $huntUserDetailId);

        $stillRemain;
        switch ($status) {
            
            case 'reveal':
                $huntAction = 'running';
                $huntUserDetail->revealed_at = now();
                $huntUserDetail->started_at = now();
                $huntUserDetail->status = 'running';
                break;
            
            case 'running':
                $huntAction = 'running';
                $huntUserDetail->started_at = new MongoDBDate();
                $huntUserDetail->status = 'running';
                break;

            case 'paused':
                $huntAction = 'paused';
                $this->calculateTheTimer($huntUserDetail,'paused');
                break;

            case 'completed':
                $this->calculateTheTimer($huntUserDetail,'completed');
                $stillRemain = $huntUserDetail->whereIn('status', ['tobestart','progress','pause'])->count();
                break;
        }
        $huntUserDetail->save();

        if ($status == 'completed' && $stillRemain == 0) {
            $fields = [ 'status'=>'completed', 'ended_at'=> new MongoDBDate(), 'finished_in'=> $huntUserDetail->sum('finished_in') ];
            $gameData = $this->takeActionOnHuntUser($huntUserDetail, '', $fields, true);
        }else if($status != 'completed'){
            $gameData = $this->takeActionOnHuntUser($huntUserDetail, $huntAction);
        }

        return response()->json(['message'=>'Action on clue has been taken successfully.', 'game_info'=> $gameData]);
    }

    public function useTheSkeletonKey(Request $request){

        $huntUserDetailId = $request->hunt_user_details_id;
        $user   = auth()->User();
        $userId = $user->id;
        
        $huntUserDetail = $this->getHuntUserDetail($userId, $huntUserDetailId);


        if ($huntUserDetail->status == 'completed') {
            return response()->json(['message'=>'You cannot use skeleton key in this clue, as it already ended.'], 422);
        }

        // $huntUser = $huntUserDetail
        //                 ->hunt_user
        //                 ->where('user_id',$user->id)
        //                 ->where('skeleton_keys.used',false)
        //                 ->first();

        // $key = User::where(['skeleton_keys.used_at' => null, '_id'=> $userId])->project(['_id'=> true, 'skeleton_keys.$'=>true])->first();
        $skeletonExists = $user->where(['skeleton_keys.used_at' => null])->project(['_id'=> true, 'skeleton_keys.$'=>true])->first();
        if ($skeletonExists) {
            
            $user->where('skeleton_keys.used_at',null)
                    ->update([
                        'skeleton_keys.$.used_at'=> new MongoDBDate()
                    ]);
        }else{
            return response()->json(['message'=>'You does not have any key to use.'], 422);
        }

        $huntFinished = $this->actionOnClue($huntUserDetail, 'completed');
        return response()->json(['message'=> 'Clue Timer has been ended successfully.', 'hunt_finished'=> $huntFinished
        ]);
    }

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

    public function generateReward($huntUserDetail)
    {
        /** Generate Reward **/
        $randNumber  = rand(1, 1000);
        // $randNumber  = 750;
        $huntUser    = $huntUserDetail->hunt_user()->select('complexity','user_id')->first();
        $complexity  = 1;
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
        if ($selectedReward->widgets_order && $selectedReward->widget_category) {
            
            $widgetCategory = $selectedReward->widget_category;
            $widgetOrder = $selectedReward->widgets_order;

            findWidget:
            $countableWidget = $widgetOrder[0];
            $userWidgets = collect($user->widgets)->pluck('id');
            $widgetItems = WidgetItem::when($widgetCategory != 'all', function ($query) use ($widgetCategory){
                                return $query->where('widget_category', $widgetCategory);
                            })
                            ->havingGender($userGender)
                            ->where('widget_name',$countableWidget)
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
        return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
    }
}
