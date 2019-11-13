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
use App\Repositories\User\UserRepository;
use App\Repositories\XPManagementRepository;
use App\Services\HuntReward\LogTheHuntRewardService;
use App\Services\User\AddRelicService;
use App\Services\User\AddXPService;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class CompleteTheClueRepository implements ClueInterface
{

    private $user;
    private $userRepository;
    private $huntUserDetail;
    private $huntUser;
    private $addXPService;

    public function __construct(){
        $this->user = auth()->user();
        $this->userRepository = new UserRepository($this->user);
        $this->addXPService = (new AddXPService)->setUser($this->user);
    }
    
    public function action($request)
    {

        // get single hunt user detail
        $this->huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);

        // get all hunt user details
        $huntUserDetails = $this->huntUserDetail->hunt_user->hunt_user_details()->get();

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
            (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $this->huntUserDetail->hunt_user_id]);
        }

        // calculate the time & mark the clue as complete
        $clueFinishedIn = (new HuntUserDetailRepository)->calculateTheTimer($this->huntUserDetail, 'rr')->finished_in;

        $rewardData['clue_completion_xp_reward'] = $this->addXPService->add($this->huntUserDetail->game->practice_games_targets->targets->sortBy('stage')->first()['xp']);
        
        $rewardData = null;
        $totalFinishedIn = $clueFinishedIn;
        if ($stillRemain == 1) {

            $rewardData['treasure_completion_xp_reward'] = $this->addXPService
                                                           ->add((new XPManagementRepository)->getModel()->where('event', 'treasure_completion')->first()->xp);
        
            // get the total completion except current clue
            $totalFinishedIn += $huntUserDetails->where('_id', '!=', $this->huntUserDetail->id)->sum('finished_in');

            // mark hunt_user as completed and set the ended_at of hunt_user 
            $dataToBeUpdate = ['status'=> 'completed', 'ended_at'=> new UTCDateTime(now()), 'finished_in'=> $totalFinishedIn];
            (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $this->huntUserDetail->hunt_user_id]);
            
            // generate the reward
            $this->huntUser   = $this->huntUserDetail->hunt_user()->select('complexity','user_id', 'relic_id')->first();
            $rewardData = $this->generateReward();
            $data = $this->addMapPiece();
            $rewardData['relic_data'] = is_array($data['relic_data'])? new stdClass(): $data['relic_data'];
            // $rewardData['agent_data'] = is_array($data['agent_data'])? new stdClass(): $data['agent_data'];
            $rewardData['collected_piece'] = $data['collected_piece'];
            $rewardData['agent_status'] = $this->user->agent_status;
        }

        // unlock the minigame if it is locked in minigame module
        $this->unlockeMiniGameIfLocked();

        // log the minigame statistic
        $this->logTheMinigmeHistory();

        //send the response
        return ['huntUserDetail'=> $this->huntUserDetail, 'rewardData'=> $rewardData, 'finishedIn'=> $totalFinishedIn];
        // return ['huntUserDetail'=> $huntUserDetail, 'rewardData'=> $rewardData];
    }

    public function unlockeMiniGameIfLocked()
    {
        return PracticeGameUser::where(['game_id'=> $this->huntUserDetail->game_id, 'user_id'=> $this->user->id])->whereNull('unlocked_at')
        ->update(['unlocked_at'=> new UTCDateTime(now())]);
    }

    public function generateReward(){

        /** Generate Reward **/
        $randNumber  = rand(1, 1000);
        $complexity  = $this->huntUser->complexity;
        // $user        = auth()->user();
        $userId      = $this->user->_id;
        $userGender  = ($this->user->avatar_detail)? $this->user->avatar_detail->gender: 'male';
        $rewards     = HuntReward::all();

        // $complexity  = 1;
        // $randNumber  = 921; // widget
        // $randNumber  = 986; // relic
        $message = [];
        $selectedReward = $rewards->where('complexity',$complexity)
        ->where('min_range', '<=', $randNumber)
        ->where('max_range','>=',$randNumber)
        ->first();

        if (!$selectedReward) {
            return [ 'reward_messages' => 'No reward found.', 'reward_data' => new stdClass()];
        }

        $rewardData['type'] = $selectedReward->reward_type;
        $rewardData['hunt_user_id'] = $this->huntUser->id;
        $rewardData['random_number'] = $randNumber;

        if ($selectedReward->widgets_order && is_array($selectedReward->widgets_order)) {

            $widgetRandNumber    = rand(1, 1000);
            $widgetOrder         = collect($selectedReward->widgets_order);
            $countableWidget     = $widgetOrder
            ->where('min', '<=', $widgetRandNumber)
            ->where('max','>=',$widgetRandNumber)
            ->first();
            
            findWidget:
            $widgetCategory  = $countableWidget['type'];
            $widgetName      = $countableWidget['widget_name'];

            $userWidgets = collect($this->user->widgets)->pluck('id');
            $widgetItems = WidgetItem::when($widgetCategory != 'all', function ($query) use ($widgetCategory){
                return $query->where('widget_category', $widgetCategory);
            })
            ->havingGender($userGender)
            ->where('widget_name',$widgetName)
            ->whereNotIn('_id',$userWidgets)
            ->select('_id', 'widget_name', 'avatar_id', 'widget_category')
            ->first();
            
            if (!$widgetItems) {

                $widgetOrder = $widgetOrder->reject(function($order) use ($widgetName, $widgetCategory) { 
                    return ($order['widget_name'] == $widgetName && $order['type'] == $widgetCategory); 
                });

                $countableWidget = $widgetOrder
                ->where('min', '!=', 0)
                ->where('max', '!=', 0)
                                    // ->sortByDesc('possibility')
                ->first();

                if ($widgetOrder->count() == 0) {
                    $rewardData['type'] = 'skeleton_key';
                    $selectedReward->skeletons = 1;
                    goto distSkeleton; 
                }
                goto findWidget; 
            }

            $widget = [ 'id'=> $widgetItems->id, 'selected'=> false ];
            $this->user->push('widgets', $widget);
            $message[] = 'Widget has been unlocked';
            $rewardData['widget'] = $widgetItems;
        }

        // if ($selectedReward->relics) {

        //     $relicRandNumber    = rand(1, 1000);
        //     $relicOrder         = collect($selectedReward->relics);
        //     $countableRelic     = $relicOrder->where('min', '<=', $relicRandNumber)->where('max','>=',$relicRandNumber)->first();

        //     relic:
        //     $relic = (new RelicRepository)->getModel()
        //                 ->active()
        //                 ->notParticipated($this->user->id)
        //                 ->whereHas('season', function($query) { $query->active(); })
        //                 ->whereDoesntHave('rewards', function($query) { $query->where('user_id', $this->user->id); })
        //                 ->where('index', $countableRelic['relic'])
        //                 ->select('_id', 'name', 'icon', 'season_id')
        //                 ->first();

        //     if (!$relic) {

        //         $relicOrder = $relicOrder->reject(function($order) use ($countableRelic) { 
        //             return ($order['relic'] == $countableRelic['relic']); 
        //         });
        //         $countableRelic = $relicOrder->where('min', '!=', 0)->where('max', '!=', 0)->sortByDesc('possibility')->first();

        //         if ($relicOrder->count() == 0) {
        //             $rewardData['type'] = 'skeleton_key';
        //             $selectedReward->skeletons = 1;
        //             goto distSkeleton; 
        //         }
        //         goto relic; 
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
            $this->user->push('skeleton_keys', $skeletons);
            $message[] = 'Skeleton key provided';
            $rewardData['skeleton_keys'] = $skeletons;
        }

        if ($selectedReward->gold_value){
            distGold:
            $this->user->gold_balance += $selectedReward->gold_value;
            $this->user->save();
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

    public function logTheMinigmeHistory()
    {
        $data['hunt_user_detail_id'] = $this->huntUserDetail->id;
        $data['game_id'] = $this->huntUserDetail->game_id;
        $data['time'] = $this->huntUserDetail->finished_in;
        $data['complexity'] = $this->huntUserDetail->hunt_user->complexity;
        (new MinigameEventFactory('hunt', 'completed', $data))->add();
    }

    public function addMapPiece()
    {
        // add relic in user's account only if:
        //      -> relic field is not null.
        //      -> all map pieces have collected.
        
        // $data = [ 'collected_piece'=> 0, 'relic_data'=> [], 'agent_data'=> [] ];
        $data = [ 'collected_piece'=> 0, 'relic_data'=> [] ];
        $relic = $this->huntUser->relic()->select('_id', 'pieces')->first();
        if ($relic) {
            $totalTreasureCompleted = $this->user->hunt_user_v1()
                                        ->where(['relic_id'=> $this->huntUser->relic_id, 'status'=> 'completed'])
                                        ->pluck('collected_piece')
                                        ->filter()
                                        ->values();

            $pieceRemaining = collect($relic->pieces)->whereNotIn('id', $totalTreasureCompleted)->pluck('id')->shuffle()->first();
            
            if (!$pieceRemaining) {
                $data['relic_data'] = (new AddRelicService)->setUser($this->user)->setRelicId($this->huntUser->relic_id)->add()->getRelic(['_id', 'complexity','icon']);
                // $data['agent_data'] = (new AddXPService)->setUser($this->user)->add(150);
                return $data;
            }else {
                $this->huntUser->collected_piece = $pieceRemaining;
                $this->huntUser->save();
                $data['collected_piece'] = $pieceRemaining;
            }
        }
        return $data;
    }
}