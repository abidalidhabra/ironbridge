<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\Factory\ClueFactory;
use App\Repositories\Hunt\HuntUserRepository;

class GetHuntParticipationDetailRepository
{
    
    public function get($huntUserId) : array
    {
        // get hunt user info
        $huntUser = (new HuntUserRepository)
                    ->where('hunt_id', $huntUserId)
                    ->whereHas('user', function($query) {
                        $query->where('_id', auth()->user()->id);
                    })
                    ->select('_id', 'user_id', 'status')
                    ->first();

            // get clues info
            $huntUserDetails = $huntUser->hunt_user_details()
            ->with(['game'=> function($query){
                $query->with('complexity_target')->select('_id','name');
            }])
            ->with('game_variation:_id,variation_name,variation_complexity,target,no_of_balls,bubble_level_id,game_id,variation_size,row,column')
            ->select('_id', 'status', 'game_id', 'game_variation_id', 'hunt_user_id', 'radius')
            ->get();

            // get non-completion clues
            $remainingClues = $huntUserDetails->where('status', '!=' ,'completed')->values();
            $totalCompletedClues = $huntUserDetails->where('status', 'completed')->values();
            
            // pause the running clues of previous running hunt
            $initializeAction = (new ClueFactory)->initializeAction('paused');
            $huntUserDetails->where('status', 'running')->values()->map(function($clue) use ($initializeAction){
                $newRequest = new Request();
                $newRequest->replace(['hunt_user_details_id'=> $clue->id]);
                $data = $initializeAction->action($newRequest);
                return $clue;
            });

        return [
            'clues_data'=> $huntUserDetails, 
            'hunt_user'=> $huntUser,
        ];
    }
}