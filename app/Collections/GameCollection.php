<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;


class GameCollection extends Collection
{
    public function getTreasureNodesTargets()
    {
        return $this->each(function ($game) {
            $game->load('treasure_nodes_target');
        });
    }
}
