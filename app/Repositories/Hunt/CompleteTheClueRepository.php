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
use App\Repositories\LootRepository;
use App\Repositories\RelicRepository;
use App\Repositories\SeasonRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\XPManagementRepository;
use App\Services\HuntReward\LogTheHuntRewardService;
use App\Services\Hunt\ChestService;
use App\Services\Hunt\LootDistribution\LootDistributionService;
use App\Services\Hunt\XPDistributionService;
use App\Services\User\AddRelicService;
use Exception;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class CompleteTheClueRepository implements ClueInterface
{

    private $user;
    private $userRepository;
    private $huntUserDetail;
    private $huntUser;
    private $xPDistributionService;
    // private $addXPService;

    public function __construct(){
        $this->user = auth()->user();
        $this->userRepository = new UserRepository($this->user);
        $this->xPDistributionService = new XPDistributionService;
        // $this->addXPService = (new AddXPService)->setUser($this->user);
    }
    
    public function action($request)
    {

        // get single hunt user detail
        $this->huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);
        $this->huntUser   = $this->huntUserDetail->hunt_user()->select('_id', 'complexity', 'user_id', 'relic_id')->first();
        $huntUserDetails  = $this->huntUserDetail->hunt_user->hunt_user_details()->get();

        // check if hunt is completed
        $stillRemain = $huntUserDetails->where('status', '!=', 'completed')->count();

        /**
         *
         * update the mark hunt_user only if
         * 1. its a last clue -> mark hunt_user as completed and set the ended_at of hunt_user
         * 2. Its a first clue (because of skeleton key use) --> mark hunt_user as running and set the start_at of hunt_user
         *  AIM: mark hunt_user as running and set the start_at of hunt_user
         */
        if($huntUserDetails->count() == $huntUserDetails->where('revealed_at', null)->count() && $huntUserDetails->where('status', 'completed')->count() == 0) {
            (new HuntUserRepository)->update(['status'=> 'running', 'started_at'=> new UTCDateTime(now())], ['_id'=> $this->huntUserDetail->hunt_user_id]);
        }

        // Calculate the time & mark the clue as complete
        // $clueFinishedIn = (new HuntUserDetailRepository)->calculateTheTimer($this->huntUserDetail, 'completed', $request->only('score'))->finished_in;
        $clueFinishedIn = (new HuntUserDetailRepository)
                            ->calculateTheTimer(
                                $this->huntUserDetail, 
                                'completed', 
                                ['score'=> $request->score, 'walked'=> $request->walked]
                            )->finished_in;

        $rewardData = null;
        $totalFinishedIn = $clueFinishedIn;
        $huntCompleted = false;
        if ($stillRemain == 1) {

            // get the total completion except current clue
            $totalFinishedIn += $huntUserDetails->where('_id', '!=', $this->huntUserDetail->id)->sum('finished_in');

            // mark hunt_user as completed and set the ended_at of hunt_user 
            $dataToBeUpdate = ['status'=> 'completed', 'ended_at'=> new UTCDateTime(now()), 'finished_in'=> $totalFinishedIn];
            (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $this->huntUserDetail->hunt_user_id]);
            
            if ($this->huntUser->relic_id) {
                $rewardData = $this->generateRelicReward();
                // $rewardData['collected_relic'] = (new AddRelicService)->setUser($this->user)->setRelicId($this->huntUser->relic_id)->activate()->getRelic(['_id', 'complexity','icon', 'number']);
                $addRelicService = (new AddRelicService)->setUser($this->user)->setRelicId($this->huntUser->relic_id)->activate();
                $rewardData['relic_info'] = $addRelicService->response();
            }else{
                
                (new ChestService)->setUser($this->user)->add();
                $rewardData['chests_bucket'] = $this->user->buckets['chests'];
            }
            $huntCompleted = true;
        }

        // $rewardData['xp_reward'] = $this->addXP($huntCompleted);
        // $rewardData['xp_reward'] = $this->xPDistributionService->setHuntUser($this->huntUser)->setUser($this->user)->add($huntCompleted);
        $rewardData['xp_state'] = $this->xPDistributionService->setHuntUser($this->huntUser)->setUser($this->user)->add($huntCompleted);
        // $rewardData['agent_status'] = $this->user->agent_status;
        // $rewardData['agent_stack'] = $this->userRepository->getAgentStack();

        if ($request->filled('score')) {
            // log the minigame statistic
            $this->logTheMinigmeHistory();
        }

        //send the response
        return ['huntUserDetail'=> $this->huntUserDetail, 'rewardData'=> $rewardData, 'finishedIn'=> $totalFinishedIn];
    }

    public function generateRelicReward() {
        
        $loots  = $this->huntUser->relic->loot_info;
        
        $lootDistributionService = new LootDistributionService;
        
        $reward = $lootDistributionService->setLoots($loots)->spin()->unbox();
        $reward->setUser(auth()->user())->open();
       
        return [
            "loot_rewards"=> array_merge(["random_number"=> $lootDistributionService->getMagicNumber()], $reward->getResponse())
        ];
    }

    public function logTheMinigmeHistory()
    {
        $data['hunt_user_detail_id'] = $this->huntUserDetail->id;
        $data['game_id'] = $this->huntUserDetail->game_id;
        $data['time'] = $this->huntUserDetail->finished_in;
        $data['complexity'] = $this->huntUserDetail->hunt_user->complexity;
        $data['score'] = $this->huntUserDetail->score;
        (new MinigameEventFactory('hunt', 'completed', $data))->add();
    }

    // public function addPiece($pieceRemaining)
    // {
    //     $this->huntUser->collected_piece = $pieceRemaining;
    //     $this->huntUser->save();
    //     return $pieceRemaining;
    // }

    // public function addMapPiece()
    // {
    //     /**
    //         -> Add relic in user's account IF:
    //             -> relic field is not null.
    //             -> all map pieces have collected.
    //     **/
    //     $data = [ 'collected_piece'=> 0, 'collected_relic'=> new stdClass ];
    //     $relic = $this->huntUser->relic_reference()->select('_id', 'pieces')->first();
    //     if ($relic) {
    //         $totalTreasureCompleted = $this->user->hunt_user_v1()
    //                                     ->where(['relic_reference_id'=> $this->huntUser->relic_reference_id, 'status'=> 'completed'])
    //                                     ->count();
                                        
    //         $totalPiecesRemaining = $relic->pieces - $totalTreasureCompleted;
    //         if ($totalPiecesRemaining <= 0) {
    //             $data['collected_relic'] = (new AddRelicService)->setUser($this->user)->setRelicId($this->huntUser->relic_reference_id)->add()->getRelic(['_id', 'complexity','icon', 'number']);
    //             $data['collected_piece'] = $this->addPiece($totalTreasureCompleted);
    //             $data['streaming_relic'] = $this->userRepository->streamingRelic();
    //             return $data;
    //         }else {
    //             $data['collected_piece'] = $this->addPiece($totalTreasureCompleted);
    //         }
    //     }
    //     return $data;
    // }

    // public function addXP($treasureCompleted)
    // {
    //     /**
    //         -> Add XP twice IF:
    //             -> relic field is not null.
    //             -> all map pieces have collected.
    //     **/
    //     $xpReward = [];
    //     if($this->user->tutorials['home']){

    //         if ($this->huntUser->relic_id) {
    //             $xp = $this->addXPForRelic($treasureCompleted);
    //         }else{
    //             $xp = $this->addXPForRandomHunt($treasureCompleted);
    //         }

    //         $xpReward = $this->addXPService->add($xp);
    //     }
    //     return (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass;
    // }

    // public function addXPForRelic($treasureCompleted)
    // {
    //     $relic = $this->huntUser->relic;
    //     $xp = $relic->completion_xp['clue'];
    //     if ($treasureCompleted) {
    //         $xp += $relic->completion_xp['treasure'];
    //     }
    //     return $xp;
    // }

    // public function addXPForRandomHunt($treasureCompleted)
    // {
    //     $xPManagementRepository = new XPManagementRepository;
    //     $complexity = $this->huntUser->complexity;
    //     $xp = $xPManagementRepository->getModel()->where(['event'=> 'clue_completion', 'complexity'=> $complexity])->first()->xp;
    //     if ($treasureCompleted) {
    //         $xp += $xPManagementRepository->getModel()->where(['event'=> 'treasure_completion', 'complexity'=> $complexity])->first()->xp;
    //     }
    //     return $xp;
    // }
}