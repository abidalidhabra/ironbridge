<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\HuntUserRepository;

class TerminatedTheLastRandomHuntRepository
{
    public function action() : array
    {
        $huntUsers = (new HuntUserRepository)
                    ->whereHas('user', function($query) {
                        $query->where('_id', auth()->user()->id);
                    })
                    ->where('status', 'paused')
                    ->select('_id', 'user_id', 'status')
                    ->first();

        if ($huntUsers) {
            $runningHuntTerminated = true;
            $huntUsers->status = 'terminated';
            $huntUsers->save();
            $huntUsers->hunt_user_details()->where('status', '!=', 'completed')->update(['status'=> 'ommited']);
        }

        return [
            'running_hunt_terminated'=> $runningHuntTerminated ?? false, 
        ];
    }
}