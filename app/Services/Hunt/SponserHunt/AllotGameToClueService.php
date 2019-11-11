<?php

namespace App\Services\Hunt\SponserHunt;

use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use Illuminate\Http\Request;
use App\Models\v2\Relic;
use Storage;

class AllotGameToClueService
{
    private $disk = 'public';

    
    public function allot(Request $request)
    {
        $miniGames = (new ParticipationInRandomHuntRepository)->randomizeGames(count($request->pieces));
        $complexity = $request->complexity;
        
        if (isset($request['status']) && $request['status'] == "edit") {
            $relic = Relic::find($request['id']);
            $pieces = [];
            $newPieces = array_keys($request->pieces);
            $k = 0;
            for ($i=0; $i < count($request->total_pieces) ; $i++) { 
                if(isset($relic->pieces[$i])){
                    $pieces[$i] = $relic->pieces[$i];
                }
                if (in_array($i, array_keys($request['pieces']))) {
                    $image = $request->pieces[$i]['image'];
                    $image->store('relics/'.$complexity, $this->disk);
                    $pieces[$i]['id'] = $i;
                    $pieces[$i]['image'] = $image->hashName();
                    
                    if (!in_array($i, array_keys($relic->pieces))) {
                        $pieces[$i]['game_id'] = $miniGames[$k]->id;
                        $pieces[$i]['game_variation_id'] = $miniGames[$k]->game_variation()->limit(1)->first()->id;                 
                    }
                    $k++;
                }
            }
            return $pieces;
        }
        return collect($request->pieces)->values()->map(function($clue, $i) use ($miniGames,$complexity) {
            
            $image = $clue['image'];
            $image->store('relics/'.$complexity, $this->disk);            
            
            $clue['id'] = $i+1;
            $clue['image'] = $image->hashName();
            $clue['game_id'] = $miniGames[$i]->id;
            $clue['game_id'] = $miniGames[$i]->id;
            $clue['game_variation_id'] = $miniGames[$i]->game_variation()->limit(1)->first()->id;
            return $clue;
        })->toArray();
    }

    public function allot_old(Request $request)
    {
        $miniGames = (new ParticipationInRandomHuntRepository)->randomizeGames(count($request->clues));
        return collect($request->clues)->values()->map(function($clue, $i) use ($miniGames) {
            $clue['radius'] = (int)$clue['radius'];
            $clue['game_id'] = $miniGames[$i]->id;
            $clue['game_variation_id'] = $miniGames[$i]->game_variation()->limit(1)->first()->id;
            return $clue;
        })->toArray();
    }
}