<?php

namespace App\Services\User;

use App\Repositories\PracticeGameUserRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\WidgetItemRepository;

class AddXPService
{

	private $user;
    private $userRepository;

    public function setUser($user) {
        $this->user = $user;
        $this->userRepository = new UserRepository($this->user);
        return $this;
    }

    // public function setAgentStatus($points = null, $level = null)
    // {
    //     $this->user->agent_status = [
    //         'level'=> ($level)?  $level: $this->user->agent_status['level'],
    //         'xp'=> ($points)? ($this->user->agent_status['xp'] + $points): $this->user->agent_status['xp'],
    //     ];
    //     return $this;
    // }

    public function add($points) {
        $this->userRepository->addXp($points);
        $data = $this->hikeAgent();
        return array_merge($data, ['agent_status'=> $this->user->agent_status]);
        // $this->userRepository->getModel()->where('_id',$this->user->id)->increment('agent_status.xp', $points);
        // $this->setAgentStatus($points);
    }

    public function hikeAgent()
    {
        $complementaries = AgentComplementary::where('agent_level', '>', $this->user->agent_status['level'])->where('xps', '<=', $this->user->agent_status['xp'])->first();
        if ($complementaries) {
            // $response['agent_level'] = $this->allotAgentLevel(1);
            $this->allotAgentLevel(1);
            
            if ($complementaries->minigames) { 
                // $response['minigames'] = $this->allotMinigames('5b0e306951b2010ec820fb4f');
                $response['minigames'] = $this->allotMinigames($complementaries->minigames);
            }
            
            if ($complementaries->bucket_size) { 
                $response['bucket_size'] = $this->allotBucketSize(5); 
            }

            if ($complementaries->widgets && $complementaries->widgets != null) { 
                // $response['widgets'] = $this->allotWidget(['5d246f0c0b6d7b19fb5ab574', '5d246f0c0b6d7b19fb5ab584']);
                $response['widgets'] = $this->allotWidget($complementaries->widgets);
            }
        }
        return $response;
    }

    public function allotAgentLevel($levelToBeIncrement)
    {
        $this->userRepository->allotAgentLevel($levelToBeIncrement);
        return $this->user->agent_status;
        // $this->userRepository->getModel()->where('_id',$this->user->id)->increment('agent_status.xp', $points);
    }

    public function allotMinigames($gameId)
    {
        $practiceGameUser = $this->user->practice_games()->where('game_id', $gameId)->first();
        return (new PracticeGameUserRepository)->unlockTheGame($practiceGameUser);
    }

    public function allotBucketSize($size)
    {
        return $this->userRepository->addSkeletonsBucket($size);
    }

    public function allotWidget($ids)
    {
        $existingWidgets = collect($this->user->widgets)->pluck('id');
        $widgetsToProvide = collect($ids)->reject(function ($id) use ($existingWidgets) {
                                return $existingWidgets->contains($id);
                            });
        return $this->userRepository->addWidgets($widgetsToProvide);
    }
}