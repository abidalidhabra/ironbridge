<?php

namespace App\Repositories\Hunt;

class GetRandomizeGameService
{
    
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function get()
    {
        return $this->user->practice_games()
                            ->with('game:id')
                            ->whereNotNull('unlocked_at')
                            ->select('_id', 'game_id', 'unlocked_at')
                            ->get()
                            ->shuffle()
                            ->pluck('game')
                            ->first();
    }
}