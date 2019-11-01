<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;

class GetLastParticipatedRandomHuntRepository
{
    
    public function get()
    {
        // get the hunt user record
        $huntUser = (new HuntUserRepository)
                    ->whereHas('user', function($query) {
                        $query->where('_id', auth()->user()->id);
                    })
                    ->where('status', 'participated')->latest()->select('_id', 'user_id', 'status', 'complexity')->first();


        if ($huntUser) {
            $runningHuntFound = true;
            $huntUserDetails = $huntUser->hunt_user_details()
                                ->with(['game'=> function($query) use ($huntUser) {
                                    $query->with(['complexity_target'=> function($query) use ($huntUser) {
                                        $query->where('complexity', $huntUser->complexity);
                                    }])
                                    ->select('_id','name');
                                }])
                                ->with('game_variation:_id,variation_name,variation_complexity,target,no_of_balls,bubble_level_id,game_id,variation_size,row,column')
                                ->select('_id', 'status', 'game_id', 'game_variation_id', 'hunt_user_id', 'radius')
                                ->get();
            $totalClues = $huntUserDetails->count();
            $completedClues = $huntUserDetails->where('status', 'completed')->count();
        }

        return [
            'participated_hunt_found'=> $runningHuntFound ?? false, 
            'total_clues'=> $totalClues ?? 0,
            'completed_clues'=> $completedClues ?? 0,
            'clues_data'=> $huntUserDetails ?? [], 
            'hunt_user'=> $huntUser, 
        ];
    }
}