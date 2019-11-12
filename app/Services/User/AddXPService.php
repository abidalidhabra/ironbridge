<?php

namespace App\Services\User;

use App\Models\v2\AgentComplementary;
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

    public function allotAgentLevel($levelToBeIncrement)
    {
        $this->userRepository->allotAgentLevel($levelToBeIncrement);
        return $this->user->agent_status;
    }

    public function allotMinigames($gameId)
    {
        $practiceGameUser = $this->user->practice_games()->whereNull('unlocked_at')->whereIn('game_id', $gameId)->with('game:_id,name')->select('_id', 'game_id', 'user_id', 'unlocked_at')->get();
        return (new PracticeGameUserRepository)->unlockTheGame($practiceGameUser);
    }

    public function allotBucketSize($size)
    {
        return $this->userRepository->addSkeletonsBucket($size);
    }

    public function allotWidget($ids)
    {
        $existingWidgets = collect($this->user->widgets)->pluck('id');
        $widgetsToProvide = collect($ids)->flatten()->filter()->values()->reject(function ($id) use ($existingWidgets) {
                                return $existingWidgets->contains($id);
                            });
        // dd(collect($ids)->flatten()->filter(), collect($ids)->flatten()->filter()->values(), $existingWidgets, $widgetsToProvide);
        return $this->userRepository->addWidgets($widgetsToProvide);
    }

    public function hikeAgent()
    {
        $complementaries = AgentComplementary::where('agent_level', '>', $this->user->agent_status['level'])->where('xps', '<=', $this->user->agent_status['xp'])->first();

        if ($complementaries) {

            $this->allotAgentLevel(1);
            
            if ($complementaries->minigames) { 
                $response['minigames'] = $this->allotMinigames($complementaries->minigames);
            }
            
            if ($complementaries->bucket_size) { 
                $response['bucket_size'] = $this->allotBucketSize(5); 
            }

            if ($complementaries->widgets) {
                $response['widgets'] = $this->allotWidget($complementaries->widgets);
            }
        }
        return $response ?? [];
    }

    public function add($points) {
        $this->userRepository->addXp($points);
        $data = $this->hikeAgent();
        return $data;
    }
}