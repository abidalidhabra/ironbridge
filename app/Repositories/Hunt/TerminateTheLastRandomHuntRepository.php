<?php

namespace App\Repositories\Hunt;

use App\Repositories\Hunt\HuntUserRepository;

class TerminateTheLastRandomHuntRepository
{
    public function terminate($isRelicHunt = false) : bool
    {
        $huntUsers = (new HuntUserRepository)
                    ->whereHas('user', function($query) {
                        $query->where('_id', auth()->user()->id);
                    })
                    ->whereNotIn('status', ['completed'])
                    ->when($isRelicHunt, function($query) {
                        $query->whereNotNull('relic_id');
                    })
                    ->when(!$isRelicHunt, function($query) {
                        $query->whereNull('relic_id');
                    })
                    ->latest()
                    ->select('_id', 'user_id', 'status')
                    ->first();

        if ($huntUsers) {
            $runningHuntTerminated = true;
            $huntUsers->status = 'terminated';
            $huntUsers->save();
            $huntUsers->hunt_user_details()->where('status', '!=', 'completed')->update(['status'=> 'terminated']);
            return true;
        }
        return false;
    }
}