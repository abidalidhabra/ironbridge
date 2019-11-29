<?php

namespace App\Repositories\Hunt;

use App\Collections\GameCollection;

class GetRandomizeGamesService
{
    
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function get(int $limit)
    {
        $games = $this->user->practice_games()
                            ->with('game:_id,name,identifier')
                            ->whereNotNull('unlocked_at')
                            ->select('_id', 'game_id', 'unlocked_at')
                            ->limit($limit)
                            ->get()
                            ->shuffle()
                            ->pluck('game');

        if ($limit > $games->count()) {
            for ($i=$games->count(); $i < $limit; $i++) { 
                $games->push($games->shuffle()->first());
            }
        }
        return new GameCollection($games);
    }
}