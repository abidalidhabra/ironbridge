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
        // $practiceGameUser = $this->user->practice_games()->whereNull('unlocked_at')->whereIn('game_id', $gameId)->with('game:_id,name')->select('_id', 'game_id', 'user_id', 'unlocked_at')->get();
        $practiceGameUser = $this->user->practice_games()->whereIn('game_id', $gameId)->with('game:_id,name')->select('_id', 'game_id', 'user_id', 'unlocked_at')->get();
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
                            })->values();
        return $this->userRepository->addWidgets($widgetsToProvide);
    }

    public function hikeAgent()
    {
        $complementaries = AgentComplementary::where('agent_level', '>', $this->user->agent_status['level'])->where('xps', '<=', $this->user->agent_status['xp'])->latest()->first();

        if ($complementaries) {

            $this->allotAgentLevel(1);
            
            if ($complementaries->minigames) { 
                $response['minigames'] = $this->allotMinigames($complementaries->minigames);
            }

            if ($complementaries->bucket_size) { 
                $this->allotBucketSize($complementaries->bucket_size); 
                $response['bucket_size'] = $complementaries->bucket_size; 
            }

            if ($complementaries->widgets) {
                $widgets = $this->allotWidget($complementaries->widgets);
                if ($widgets->count()) {
                    $response['widgets'] = $widgets;
                }
            }

            if ($complementaries->nodes) {

                $nodes = collect();

                if (isset($complementaries->nodes['mg_challenge'])) {
                    $nodes->put('mg_challenge', collect(['action'=> true]));
                }

                if (isset($complementaries->nodes['power'])) {
                    $nodes->put('power', collect(['action'=> true]));
                    if (isset($complementaries->nodes['power']['value']) && ($complementaries->nodes['power']['value'] > 0)) {
                        $nodes['power']->put('value', $complementaries->nodes['power']['value']);
                    }
                }

                if (isset($complementaries->nodes['bonus'])) {
                    $nodes->put('bonus', collect(['action'=> true]));
                }

                $response['nodes'] = $this->userRepository->addNodes($nodes);
            }
        }
        return $response ?? [];
    }

    public function add($points) {
        $this->userRepository->addXp($points);
        $data = $this->hikeAgent();
        // $this->userRepository->allotAgentLevel(-1); // static
        // $this->userRepository->addXp(($points * -1)); // static
        return $data;
    }
}