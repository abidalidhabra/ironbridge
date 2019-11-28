<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Repositories\Hunt\Contracts\HuntParticipationInterface;
use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use App\Repositories\Hunt\TerminatedTheLastRandomHuntRepository;
use App\Repositories\RelicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParticipationInSeasonalHuntRepository implements HuntParticipationInterface
{
    private $user;
    private $relic;

    public function participate($request)
    {
        $this->user = auth()->user();
        (new TerminatedTheLastRandomHuntRepository)->terminate(true);
        $this->relic = (new RelicRepository)->find($request->relic_id);
        $huntUser = $this->add($request);
        $clueDetails = $this->addClues($request, $huntUser);

        $data = (new GetRelicHuntParticipationRepository)->get($huntUser->id);

        return [
            'hunt_user'=> $data['hunt_user'],
            'clues_data'=> $data['clues_data'],
        ];
    }

    public function add($request) : HuntUser
    {
        return $this->user->hunt_user_v1()->create([
            'relic_id'=> $this->relic->id,
            'complexity'=> $this->relic->complexity,
            'estimated_time'=> $this->getEpproximatedTime($request->total_clues),
            'location'=> [
                'type'=> "Point",
                'coordinates'=> [0, 0]
            ]
        ]);
    }

    public function addClues($request, $huntUser) : Collection
    {

        // Get the games in random orders
        $miniGames = (new ParticipationInRandomHuntRepository)->setUser($this->user)->randomizeGames($request->total_clues);

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

    public function getEpproximatedTime($totalClues)
    {
        if ($this->relic->complexity == 1) {
            $distance = 50; // Level 1 hunts have clues 50 meters apart. 3 stops per hunt.
        }else if ($this->relic->complexity == 2) {
            $distance = 100; // Level 2 hunts have clues 100 meters apart. 3-4 stops per hunt.
        }else if ($this->relic->complexity == 3) {
            $distance = 250; // Level 3 hunts have clues 250 meters apart. 3-4 stops per hunt.
        }else if ($this->relic->complexity == 4) {
            $distance = 500; // Level 4 hunts have clues 500 meters apart. 4-5 stops per hunt.
        }else if ($this->relic->complexity == 5) {
            $distance = 1000; // Level 5 hunts have clues 1000 meters apart. 4-5 stops per hunt.
        }

        $distance = $distance / 1000; // This distance in km.
        $minutesToBeTaken = ((((60 / 4.5) * $distance) + 5) * $totalClues);
        return round($minutesToBeTaken * 60, 2);
    }
}