<?php

namespace App\Repositories\Hunt;

use App\Http\Controllers\Api\Hunt\RandomHuntController;
use App\Http\Controllers\Api\v2\HuntController;
use App\Models\v1\Game;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Repositories\Game\GameRepository;
use App\Repositories\Hunt\Contracts\HuntParticipationInterface;
use App\Repositories\Hunt\GetHuntParticipationDetailRepository;
use App\Repositories\RelicRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParticipationInSeasonalHuntRepository implements HuntParticipationInterface
{
    private $user;
    private $relic;

    public function participate($request)
    {
        $this->user = auth()->user();
        $this->relic = (new RelicRepository)->find($request->relic_id);
        $availableGold = (new UserRepository($this->user))->deductGold($this->relic->fees);
        $huntUser = $this->add($request);
        $clueDetails = $this->addClues($request, $huntUser);

        $data = (new GetHuntParticipationDetailRepository)->get($huntUser->id);

        return [
            'hunt_user'=> $data['hunt_user'],
            'clues_data'=> $data['clues_data'],
            'available_gold'=> $availableGold,
        ];
    }

    public function add($request) : HuntUser
    {
        return $this->user->hunt_user_v1()->create([
            'hunt_id'=> $this->relic->id,
            'complexity'=> $this->relic->complexity,
            'estimated_time'=> $this->getEpproximatedTime(),
            'location'=> [
                'type'=> "Point",
                'coordinates'=> [0, 0]
            ] 
        ]);
    }

    public function addClues($request, $huntUser) : Collection
    {

        $huntUserDetails = collect();
        foreach ($this->relic->clues as $index => $clue) {
            $huntUserDetails[] = new HuntUserDetail([
                'index' => ($index + 1),
                'name' => $clue['name'],
                'desc' => $clue['desc'],
                'game_id'  => $clue['game_id'],
                'radius'  => $clue['radius'],
                'game_variation_id' => $clue['game_variation_id'],
            ]);
        }
        return $huntUser->hunt_user_details()->saveMany($huntUserDetails);
    }

    public function getEpproximatedTime()
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
        $minutesToBeTaken = ((((60 / 4.5) * $distance) + 5) * count($this->relic->clues));
        return $minutesToBeTaken * 60;
    }
}