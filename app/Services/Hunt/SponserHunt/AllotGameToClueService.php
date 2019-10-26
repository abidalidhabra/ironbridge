<?php

namespace App\Services\Hunt\SponserHunt;

use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use Illuminate\Http\Request;

class AllotGameToClueService
{
    
    public function allot(Request $request)
    {
        $miniGames = (new ParticipationInRandomHuntRepository)->randomizeGames(count($request->clues));
        return collect($request->clues)->values()->map(function($clue, $i) use ($miniGames) {
            $clue['game_id'] = $miniGames[$i]->id;
            $clue['game_variation_id'] = $miniGames[$i]->game_variation()->limit(1)->first()->id;
            return $clue;
        })->toArray();
    }
}