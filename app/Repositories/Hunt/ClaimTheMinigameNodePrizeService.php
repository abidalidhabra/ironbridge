<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntStatistic;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\MGCLootRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\XPManagementRepository;
use App\Services\Hunt\LootDistribution\LootDistributionService;
use App\Services\User\AddXPService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class ClaimTheMinigameNodePrizeService
{

    private $user;
    private $gameId;
    // private $xPManagementRepository;
    private $huntStatisticRepository;

    public function __construct()
    {
        $this->huntStatisticRepository = (new HuntStatisticRepository)->first(['id', 'freeze_till', 'mgc_xp']);
        // $this->xPManagementRepository = (new XPManagementRepository)->where(['event'=> 'clue_completion', 'complexity'=> 1])->first();
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        return $this;
    }

    public function do()
    {
        
        // add the xp in users account
        $xpReward = (new AddXPService)->setUser($this->user)->add($this->huntStatisticRepository->mgc_xp)->response();
        $rewardData['xp_state'] = (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass;
        // $rewardData['agent_status'] = $this->user->agent_status;

        /** Reward system */
        $loots = (new MGCLootRepository)->all();
        $lootDistributionService = new LootDistributionService;
        $reward = $lootDistributionService->setLoots($loots)->spin()->unbox()->setUser($this->user)->open();
        $rewardData['loot_rewards'] = $reward->getResponse();
        // $rewardData['reward_data'] = $reward->getResponse();
        /** Reward system */
        
        $rewardData['mingiame_info'] = $this->markMGCAsComplete();
        
        // get the agent stack
        // $rewardData['agent_stack'] = (new UserRepository($this->user))->getAgentStack();
        
        return $rewardData;
    }

    public function markMGCAsComplete()
    {
        $games = $this->user->mgc_status->where('game_id', '!=', $this->gameId)->map(function($minigame) {
                        if ($minigame['completed_at']) {
                            $minigame['completed_at'] = new UTCDateTime;
                        }
                        return $minigame;
                    });
        
        $games->push(
            $game = $this->user->mgc_status->where('game_id', $this->gameId)->map(function($minigame) {
                        $minigame['completed_at'] = new UTCDateTime;
                        return $minigame;
                    })->first()
        );
        
        $completedAt = Carbon::createFromTimestamp($game['completed_at']->toDateTime()->getTimestamp())
                        ->addSeconds($this->huntStatisticRepository->freeze_till['mgc']);

        $remainingFreezeTime = ($completedAt->gte(now()))? $completedAt->diffInSeconds(now()): 0;

        $this->user->mgc_status = $games->values()->toArray();
        $this->user->save();

        return [
            'remaining_seconds' => $remainingFreezeTime,
            'game_id'=> $game['game_id']
        ];
    }

    // public function generateRelicReward() {

    //     $loots = (new MGCLootRepository)->first();
    //     dd($loots);
    //     if ($loot->skeletons){
    //         distSkeleton:
    //         $skeletons = [];
    //         for ($i=0; $i < $loot->skeletons; $i++) { 
    //             $skeletons[] = [
    //                 'key'       => strtoupper(substr(uniqid(), 0, 10)),
    //                 'created_at'=> new UTCDateTime(),
    //                 'used_at'   => null
    //             ];
    //         }
    //         $this->user->push('skeleton_keys', $skeletons);
    //         $message[] = 'Skeleton key provided';
    //         $rewardData['skeleton_keys'] = $skeletons;
    //     }

    //     if ($loot->gold_value){
    //         distGold:
    //         $this->user->gold_balance += $loot->gold_value;
    //         $this->user->save();
    //         $message[] = 'Gold provided.';
    //         $rewardData['golds'] = $loot->gold_value;
    //     }
        
    //     $rewardData['user_id'] = $this->user->id;
    //     return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
    // }
}