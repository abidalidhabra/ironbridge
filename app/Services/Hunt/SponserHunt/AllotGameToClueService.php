<?php

namespace App\Services\Hunt\SponserHunt;

use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use Illuminate\Http\Request;

class AllotGameToClueService
{
    
    public function allot(Request $request)
    {
        $hunts = collect($request->hunts)->values()->map(function($hunt) {
            $miniGames = (new ParticipationInRandomHuntRepository)->randomizeGames(count($hunt['clues']));
            $hunt['clues'] = collect($hunt['clues'])->values()->map(function($clue, $i) use ($miniGames) {
                $clue['complexity'] = (int) $clue['complexity'];
                $clue['game_id'] = $miniGames[$i]->id;
                $clue['game_variation_id'] = $miniGames[$i]->game_variation()->limit(1)->first()->id;
                return $clue;
            })->toArray();
            return $hunt;
        });
        return $hunts->toArray();
    }
}