<?php

namespace App\Repositories\Hunt;

use App\Factories\MinigameEventFactory;
use App\Models\v1\WidgetItem;
use App\Models\v2\HuntReward;
use App\Models\v2\HuntUserDetail;
use App\Models\v2\PracticeGameUser;
use App\Repositories\Hunt\Contracts\ClueInterface;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;
use App\Repositories\RelicRepository;
use App\Repositories\SeasonRepository;
use App\Services\HuntReward\LogTheHuntRewardService;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class CompleteTheClueRepository implements ClueInterface
{
    
    public function action($request)
    {

        // get single hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);

        // get all hunt user details
        $huntUserDetails = $huntUserDetail->hunt_user->hunt_user_details()->get();

        // check if hunt is completed
        $stillRemain = $huntUserDetails->where('status', '!=', 'completed')->count();

        /**
         *
         * update the mark hunt_user only if
         * 1. its a last clue -> mark hunt_user as completed and set the ended_at of hunt_user
         * 2. Its a first clue (because of skeleton key use) --> mark hunt_user as running and set the start_at of hunt_user
         */
        if($huntUserDetails->count() == $huntUserDetails->where('revealed_at', null)->count() && $huntUserDetails->where('status', 'completed')->count() == 0) {
            
            // mark hunt_user as running and set the start_at of hunt_user 
            $dataToBeUpdate = ['status'=> 'running', 'started_at'=> new UTCDateTime(now())];
            (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $huntUserDetail->hunt_user_id]);
        }

        // calculate the time & mark the clue as complete
        $clueFinishedIn = (new HuntUserDetailRepository)->calculateTheTimer($huntUserDetail, 'completed')->finished_in;
        
        $rewardData = null;
        $totalFinishedIn = $clueFinishedIn;
        if ($stillRemain == 1) {
            
            // get the total completion except current clue
            $totalFinishedIn += $huntUserDetails->where('_id', '!=', $huntUserDetail->id)->sum('finished_in');

            // mark hunt_user as completed and set the ended_at of hunt_user 
            $dataToBeUpdate = ['status'=> 'completed', 'ended_at'=> new UTCDateTime(now()), 'finished_in'=> $totalFinishedIn];
            (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $huntUserDetail->hunt_user_id]);
            
            // generate the reward
            $rewardData = $this->generateReward($huntUserDetail);
        }

        // unlock the minigame if it is locked in minigame module
        $this->unlockeMiniGameIfLocked($huntUserDetail->game_id, auth()->user()->id);

        // log the minigame statistic
        $this->logTheMinigmeHistory($huntUserDetail);

        //send the response
        return ['huntUserDetail'=> $huntUserDetail, 'rewardData'=> $rewardData, 'finishedIn'=> $totalFinishedIn];
        // return ['huntUserDetail'=> $huntUserDetail, 'rewardData'=> $rewardData];
    }

    public function unlockeMiniGameIfLocked($completedGameId, $userId)
    {
        return PracticeGameUser::where(['game_id'=> $completedGameId, 'user_id'=> $userId])->whereNull('unlocked_at')
                ->update(['unlocked_at'=> new UTCDateTime(now())]);
    }

    public function generateReward($huntUserDetail){

        /** Generate Reward **/
        $randNumber  = rand(1, 1000);
        // $randNumber = 986;
        $huntUser    = $huntUserDetail->hunt_user()->select('complexity','user_id')->first();
        $complexity  = $huntUser->complexity;
        // $complexity  = 1;
        $user        = auth()->user();
        $userId      = $user->_id;
        $userGender  = ($user->avatar_detail)?$user->avatar_detail->gender:'male';
        $rewards     = HuntReward::all();

        $selectedReward = $rewards->where('complexity',$complexity)->where('min_range', '<=', $randNumber)->where('max_range','>=',$randNumber)->first();
        if (!$selectedReward) {
            return [ 'reward_messages' => 'No reward found.', 'reward_data' => new stdClass()];
        }
        $rewardData['type'] = $selectedReward->reward_type;
        $rewardData['hunt_user_id'] = $huntUser->id;

        $rewardData['random_number'] = $randNumber;

        if ($selectedReward->widgets_order && is_array($selectedReward->widgets_order)) {
            
            $widgetRandNumber    = rand(1, 1000);
            // $widgetRandNumber    = 301;
            $widgetOrder         = collect($selectedReward->widgets_order);
            $gotchaDesiredWidget = true;
            $countableWidget     = $widgetOrder->where('min', '<=', $widgetRandNumber)->where('max','>=',$widgetRandNumber)->first();
            // $widgetOrder     = $selectedReward->widgets_order;
            
            findWidget:
            // $countableWidget = $widgetOrder[0];
            $widgetCategory  = $countableWidget['type'];
            $widgetName      = $countableWidget['widget_name'];

            // $user = User::where('_id', auth()->user()->id)->select('_id', 'widgets')->first();
            // $userWidgets = collect($user->widgets)->pluck('id');
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
                // $widgetOrder = array_splice($widgetOrder, 1);
                // if (count($widgetOrder) == 0) { goto distSkeleton; }

                // $widgetOrder = $widgetOrder->where('widget_category', '!=', $widgetCategory)->where('widget_name', '!=' ,$widgetName);
                $widgetOrder = $widgetOrder->where('type', '!=', $widgetCategory)->where('widget_name', '!=' ,$widgetName);
                $countableWidget = $widgetOrder->first();
                if ($widgetOrder->count() == 0) {
                    $rewardData['type'] = 'skeleton_key';
                    $selectedReward->skeletons = 1;
                    goto distSkeleton; 
                }
                goto findWidget; 
            }

            $widget = [ 'id'=> $widgetItems->id, 'selected'=> false ];
            $user->push('widgets', $widget);
            $message[] = 'Widget has been unlocked';
            $rewardData['widget'] = $widgetItems;
        }

        // if ($selectedReward->relics) {

        //     $relicRandNumber    = rand(1, 1000);
        //     $relicOrder         = collect($selectedReward->relics);
        //     $countableRelic     = $relicOrder->where('min', '<=', $relicRandNumber)->where('max','>=',$relicRandNumber)->first();

        //     relic:
        //     $relicsCount = (new RelicRepository)->getModel()->active()->notParticipated(auth()->user()->id)
        //                     ->whereHas('season', function($query) { $query->active(); })->count();
            
        //     $relic = (new RelicRepository)->getModel()
        //                 ->active()
        //                 ->notParticipated(auth()->user()->id)
        //                 ->whereHas('season', function($query) { $query->active(); })
        //                 ->whereDoesntHave('rewards', function($query) { $query->where('user_id', auth()->user()->id); })
        //                 ->select('_id', 'name', 'icon', 'season_id')
        //                 ->skip(rand(0,$relicsCount-1))
        //                 ->take(1)
        //                 ->first();

        //     if (!$relic) {
        //         $rewardData['type'] = 'skeleton_key';
        //         $selectedReward->skeletons = 1;
        //         goto distSkeleton; 
        //     }

        //     $message[] = 'Relic provided.';
        //     $rewardData['relic_id'] = $relic->id;
        //     $rewardData['relic'] = ['id'=> $relic->id, 'icon'=> $relic->icon];
        // }

        if ($selectedReward->skeletons){
            distSkeleton:
            $skeletons = [];
            for ($i=0; $i < $selectedReward->skeletons; $i++) { 
                $skeletons[] = [
                    'key'       => strtoupper(substr(uniqid(), 0, 10)),
                    'created_at'=> new UTCDateTime() ,
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

        $rewardData['user_id'] = $userId;
        (new LogTheHuntRewardService)->add($rewardData);
        unset($selectedReward->min_range, $selectedReward->max_range);
        unset($rewardData['hunt_user_id'], $rewardData['user_id'], $rewardData['type']);
        Log::info([ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData]);
        return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
    }

    public function logTheMinigmeHistory($huntUserDetail)
    {
        $data['hunt_user_detail_id'] = $huntUserDetail->id;
        $data['game_id'] = $huntUserDetail->game_id;
        $data['time'] = $huntUserDetail->finished_in;
        $data['complexity'] = $huntUserDetail->hunt_user->complexity;
        (new MinigameEventFactory('hunt', 'completed', $data))->add();
    }
}