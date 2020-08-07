<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\Factory\ClueFactory;
use App\Repositories\Hunt\HuntUserRepository;
use Illuminate\Http\Request;

class GetRelicHuntParticipationRepository
{
    
    public function get($huntUserId) : array
    {
        // get hunt user info
        $huntUser = (new HuntUserRepository)
                    ->where('_id', $huntUserId)
                    ->select('_id', 'user_id', 'status', 'complexity', 'estimated_time', 'relic_id')
                    ->first();
            
            // get clues info
            $huntUserDetails = $huntUser->hunt_user_details()
                            ->with(['game'=> function($query) use ($huntUser) {
                                $query->with(['complexity_target'=> function($query) use ($huntUser) {
                                    $query->where('complexity', $huntUser->complexity);
                                }])
                                ->select('_id','name');
                            }])
                            ->with('game_variation:_id,variation_name,variation_complexity,target,no_of_balls,bubble_level_id,game_id,variation_size,row,column')
                            ->select('_id', 'status', 'game_id', 'game_variation_id', 'hunt_user_id', 'radius', 'index')
                            ->get();

            // pause the running clues of previous running hunt
            $initializeAction = (new ClueFactory)->initializeAction('paused');
            $huntUserDetails->where('status', 'running')->values()->map(function($clue) use ($initializeAction){
                $newRequest = new Request();
                $newRequest->replace(['hunt_user_details_id'=> $clue->id]);
                $data = $initializeAction->action($newRequest);
                return $clue;
            });

        $remainingClues = $huntUserDetails->where('status', '!=' ,'completed')->count();
        $completedClues = $huntUserDetails->where('status', 'completed')->count();
        $relic = $huntUser->relic()->select('_id', 'name', 'pieces', 'number')->first();
        $relic->collected_pieces = $relic->map_pieces()->where(['user_id'=> $huntUser->user_id])->count();
        return [
            'relic_info'=> $relic,
            'clues_data'=> $huntUserDetails, 
            'hunt_user'=> $huntUser,
            'total_remaining_clues'=> $remainingClues,
            'total_completed_clues'=> $completedClues
        ];
    }
}