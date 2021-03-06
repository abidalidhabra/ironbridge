<?php

namespace App\Services\MiniGame;

use App\Collections\GameCollection;
use App\Repositories\Game\GameRepository;
use App\Services\Traits\UserTraits;

class MiniGameInfoService
{

    use UserTraits;

    public function chestMiniGame()
    {
        if(isset($this->user->buckets['chests']['minigame_id'])) {
            $game =  $this->get($this->user->buckets['chests']['minigame_id']);
        }else{
            $game = $this->random(1);
        }

        $game = $game->load(['complexity_target'=> function($query) {
                    $query->where('complexity', 1);
                },'single_game_variation'=> function($query) {
                    $query->limit(1)->select('_id','variation_name','variation_complexity','target','no_of_balls','bubble_level_id','game_id','variation_size','variation_image','row','column');
                }])
                ->first();

        if ($game->single_game_variation->variation_image) {
            $variation = $game->single_game_variation->toArray();
            $image = collect($variation['variation_image'])->values()->shuffle()->first();
            $variation['variation_image'] = $image;
        }
        $game->game_variation = $variation ?? $game->single_game_variation;
        unset($game->single_game_variation);
        return $game;
    }

    public function random($limit)
    {
        return new GameCollection(
            $this->user->practice_games()
                    ->with('game:_id,name,identifier')
                    ->whereNotNull('unlocked_at')
                    ->select('_id', 'game_id', 'unlocked_at')
                    ->get()
                    ->shuffle()
                    ->pluck('game')
                    ->take($limit)
        );
    }

    public function get($id)
    {
        return (new GameRepository)->where('_id', $id)->select('_id','name','identifier')->get();
    }
}