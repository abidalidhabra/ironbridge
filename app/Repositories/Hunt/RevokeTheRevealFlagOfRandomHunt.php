<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\Factory\ClueFactory;
use App\Repositories\Hunt\HuntUserRepository;
use Illuminate\Http\Request;

class RevokeTheRevealFlagOfRandomHunt
{
    
    public function revoke() : array
    {
        // get hunt user info
        $huntUser = (new HuntUserRepository)
                    ->whereHas('user', function($query) {
                        $query->where('_id', auth()->user()->id);
                    })
                    ->whereIn('status', ['participated', 'running', 'paused'])
                    ->latest()->select('_id', 'user_id', 'status')->first();

        if ($huntUser) {
            $runningHuntFound = true;

            // get clues info
            $clues = $huntUser->hunt_user_details()
                    ->with(['game'=> function($query){
                        $query->with('complexity_target')->select('_id','name');
                    }])
                    ->with('game_variation:_id,variation_name,variation_complexity,target,no_of_balls,bubble_level_id,game_id,variation_size,row,column')
                    ->select('_id', 'status', 'game_id', 'game_variation_id', 'hunt_user_id', 'radius')
                    ->get();

            // get non-completion clues
            $remainingClues = $clues->where('status', '!=' ,'completed')->values();
            $totalCompletedClues = $clues->where('status', 'completed')->values();
            
            // pause the running clues of previous running hunt
            $initializeAction = (new ClueFactory)->initializeAction('paused');
            $clues->where('status', 'running')->values()->map(function($clue) use ($initializeAction){
                $newRequest = new Request();
                $newRequest->replace(['hunt_user_details_id'=> $clue->id]);
                $data = $initializeAction->action($newRequest);
                return $clue;
            });
        }

        return [
            'hunt_user'=> $huntUser,
            'running_hunt_found'=> $runningHuntFound ?? false, 
            'remaining_clues'=> $remainingClues ?? [],
            'total_remaining_clues'=> (isset($remainingClues)) ? $remainingClues->count(): 0,
            'total_completed_clues'=> (isset($totalCompletedClues)) ? $totalCompletedClues->count(): 0,
        ];
    }
}