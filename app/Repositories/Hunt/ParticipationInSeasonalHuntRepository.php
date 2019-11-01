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
        
        $huntUser = $this->add($request);
        $clueDetails = $this->addClues($request, $huntUser);

        $data = (new GetHuntParticipationDetailRepository)->get($huntUser->id);

        return [
            'hunt_user'=> $data['hunt_user'],
            'clues_data'=> $data['clues_data'],
        ];
    }

    public function add($request) : HuntUser
    {
        return $this->user->hunt_user_v1()->create([
            'hunt_id'=> $this->relic->id,
            'complexity'=> $this->relic->complexity,
            'location'=> [
                'type'=> "Point",
                'coordinates'=> [0, 0]
            ] 
        ]);
    }

    public function addClues($request, $huntUser) : Collection
    {

        $huntUserDetails = collect();
        foreach ($this->relic['clues'] as $index => $clue) {
            $huntUserDetails[] = new HuntUserDetail([
                'name' => $clue['name'],
                'desc' => $clue['desc'],
                'game_id'  => $clue['game_id'],
                'radius'  => $clue['radius'],
                'game_variation_id' => $clue['game_variation_id'],
            ]);
        }
        return $huntUser->hunt_user_details()->saveMany($huntUserDetails);
    }
}