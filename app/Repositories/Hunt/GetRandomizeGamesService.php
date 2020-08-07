<?php

namespace App\Repositories\Hunt;

use App\Collections\GameCollection;
use App\Repositories\PracticeGameUserRepository;

class GetRandomizeGamesService
{
    
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }


    public function getFreezedGames()
    {
        return $this->user->mgc_status->filter(function($status) {
            return (
                $status['completed_at'] < now()->subHours(4) ||
                $status['completed_at'] == null
            );
        });
    }

    public function get(int $limit)
    {

        $freezedGames = $this->getFreezedGames();

        $games = $this->user->practice_games()
                            ->with('game:_id,name,identifier')
                            ->whereIn('game_id', $freezedGames->pluck('game_id'))
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

    public function first($id = null, $excluededIds = [])
    {

        $game = $this->user->practice_games()
                ->with('game:_id,name,identifier')
                ->when(count($excluededIds), function($query) use ($excluededIds){
                    $query->whereNotIn('game_id', $excluededIds);
                })
                ->when($id, function($query) use ($id){
                    $query->where('game_id', $id);
                })
                ->when(!$id, function($query) use ($id){
                    $query->whereNotNull('unlocked_at');
                })
                ->select('_id', 'game_id', 'unlocked_at')
                ->get()
                ->shuffle()
                ->pluck('game')
                ->first();

        if (!$game) {
            $game = $this->get(0)->first();
        }

        return $game->load('treasure_nodes_target');
    }
}