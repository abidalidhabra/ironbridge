<?php

namespace App\Repositories\Hunt;

use App\Http\Controllers\Api\Hunt\RandomHuntController;
use App\Http\Controllers\Api\v2\HuntController;
use App\Models\v1\Game;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Repositories\Game\GameRepository;
use App\Repositories\Hunt\Contracts\HuntParticipationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParticipationInRandomHuntRepository implements HuntParticipationInterface
{
    private $user;

    public function participate($request)
    {
        $this->user = auth()->user();
        
        $bypassStatus = $this->bypassPreviousHunt();
        $huntUser = $this->add($request);
        $clueDetails = $this->addClues($request, $huntUser);
        return [
            'bypass_previous_hunt'  => $bypassStatus,
            'hunt'  => $huntUser->setVisible(['_id', 'user_id', 'complexity']),
            'clues_data'  => $clueDetails->map(function($clue) {
                return $clue->only(['_id', 'hunt_user_id', 'game_variation_id', 'game_id', 'radius', 'status']);
            })
        ];
    }

    public function add($request) : HuntUser
    {
        return $this->user->hunt_user_v1()->create([
            'complexity'=> $request->complexity,
            'location'=> [
                'type'=> "Point",
                'coordinates'=> [(float)$request->longitude, (float)$request->latitude]
            ] 
        ]);
    }

    public function addClues($request, $huntUser) : Collection
    {

        // Get the games in random orders
        $miniGames = $this->randomizeGames($request->total_clues, $request->complexity);
        
        // Reface the games in random orders
        $huntUserDetails = collect();
        for ($i=0; $i < $request->total_clues; $i++) { 
            $huntUserDetails[] = new HuntUserDetail([
                'location' => null,
                'game_id'  => $miniGames[$i]->id,
                'radius'   => 5,
                'game_variation_id' => $miniGames[$i]->game_variation()->limit(1)->first()->id,
            ]);
        }
        return $huntUser->hunt_user_details()->saveMany($huntUserDetails);
    }

    public function randomizeGames(int $noOfClues, int $complexity) :Collection
    {
        return (new GameRepository)
                ->getModel()
                ->where('status', true)
                ->whereIn('identifier', ['bubble_shooter', 'block', 'snake', 'domino', 'slices', 'hexa', 'sudoku'])
                ->get(['_id'])
                ->shuffle()
                ->take($noOfClues);
    }

    public function bypassPreviousHunt()
    {
        $data = (new GetLastRunningRandomHuntRepository)->get();
        if ($data['running_hunt_found']) {
            $bypassStatus = true;
            $data['hunt_user']->status = 'skipped';
            $data['hunt_user']->save();
            $data['hunt_user']->hunt_user_details()->where('status', '!=', 'completed')->update(['status'=> 'skipped']);
        }
        return $bypassStatus ?? false;
    }
}