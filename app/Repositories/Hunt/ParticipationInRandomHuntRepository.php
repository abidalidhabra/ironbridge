<?php

namespace App\Repositories\Hunt;

use App\Http\Controllers\Api\Hunt\RandomHuntController;
use App\Http\Controllers\Api\v2\HuntController;
use App\Models\v1\Game;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Models\v2\Relic;
use App\Repositories\Game\GameRepository;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\Contracts\HuntParticipationInterface;
use App\Repositories\Hunt\GetLastParticipatedRandomHuntRepository;
use App\Repositories\Hunt\TerminateTheLastRandomHuntRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParticipationInRandomHuntRepository implements HuntParticipationInterface
{
    private $user;
    private $userRepository;

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function participate($request)
    {
        $this->user = auth()->user();
        $this->userRepository = new UserRepository($this->user);
        
        (new TerminateTheLastRandomHuntRepository)->terminate();
        
        $huntUser = $this->add($request);
        $clueDetails = $this->addClues($request, $huntUser);

        $data = (new GetLastParticipatedRandomHuntRepository)->get();

        return [
            'participated_hunt_found'=> $data['participated_hunt_found'], 
            'total_clues'=> $data['total_clues'],
            'completed_clues'=> $data['completed_clues'],
            'hunt_user'=> $data['hunt_user'],
            'clues_data'=> $data['clues_data'],
        ];
    }

    public function add($request) : HuntUser
    {
        // $relic = $this->userRepository->streamingRelic();
        return $this->user->hunt_user_v1()->create([
            'complexity'=> (int)$request->complexity,
            // 'relic_reference_id'=> ($relic)? $relic->id: null,
            'location'=> [
                'type'=> "Point",
                'coordinates'=> [(float)$request->longitude, (float)$request->latitude]
            ] 
        ]);
    }

    public function addClues($request, $huntUser) : Collection
    {
        // Get the games in random orders
        $miniGames = $this->randomizeGames($request->total_clues);

        // Reface the games in random orders
        $huntUserDetails = collect();
        for ($i=0; $i < $request->total_clues; $i++) { 
            $huntUserDetails[] = new HuntUserDetail([
                'index' => ($i + 1),
                'location' => null,
                'game_id'  => $miniGames[$i]->id,
                'radius'   => 5,
                'game_variation_id' => $miniGames[$i]->game_variation()->limit(1)->first()->id,
            ]);
        }
        return $huntUser->hunt_user_details()->saveMany($huntUserDetails);
    }

    public function randomizeGames(int $noOfClues) :Collection
    {
        $userMiniGames = collect();
        $userMiniGames['locked'] = collect();
        if ($this->user) {
            $userMiniGames['locked'] = $this->user->practice_games()
                                        ->with('game:id')
                                        ->whereNotNull('unlocked_at')
                                        ->limit($noOfClues)
                                        ->select('_id', 'game_id', 'unlocked_at')
                                        ->get()
                                        ->shuffle()
                                        ->pluck('game');
        }
                                    
        $minigamesNeeded = $noOfClues - $userMiniGames['locked']->count();
        if ($minigamesNeeded > 0) {
            $userMiniGames['random'] =  (new GameRepository)
                                        ->getModel()
                                        ->where('status', true)
                                        ->whereNotIn('id', $userMiniGames['locked']->pluck('id'))
                                        ->whereIn('identifier', ['bubble_shooter', 'block', 'snake', 'domino', 'slices', 'hexa', 'sudoku'])
                                        ->get(['id'])
                                        ->shuffle()
                                        ->take($minigamesNeeded);
        }
        
        return $userMiniGames->flatten();
    }

    // public function getEpproximatedTime($totalClues)
    // {
    //     $data = (new HuntStatisticRepository)->first(['id', 'distances.random_hunt']);
    //     $distance = $data->distances['random_hunt'] / 1000; // This distance in km.
    //     $minutesToBeTaken = ((((60 / 4.5) * $distance) + 5) * $totalClues);
    //     return round($minutesToBeTaken * 60, 2);
    // }
}