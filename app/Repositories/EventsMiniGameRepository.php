<?php

namespace App\Repositories;

use App\Models\v2\EventsMinigame;
use App\Repositories\Contracts\EventsMiniGameInterface;

class EventsMiniGameRepository implements EventsMiniGameInterface
{

	function createByEventsUser($eventUser, $data)
    {
        return $eventUser->minigames()->createMany($data);
    }

    public function addCompletion($id, $eventMiniGameUniqueId, $dataToPush)
    {
        return EventsMinigame::where('_id', $id)->where('mini_games._id', $eventMiniGameUniqueId)
        		->push('mini_games.$.completions', $dataToPush);
    }

    public function getStatus($id)
    {
    	return EventsMinigame::where('_id', $id)->select('_id', 'from', 'to', 'status')->first();
    }
}