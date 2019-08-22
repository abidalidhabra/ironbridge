<?php

namespace App\Refacing;

use App\Refacing\DayWiseMGOutputInterface;
use App\Refacing\TodaysMinigameInteface;

class DayWiseMGOutput implements DayWiseMGOutputInterface, TodaysMinigameInteface {

    /**
     * Reface the Event's daywise minigames response.
     *
     * @param  App\Models\v2\EventsMinigame
     * @param  string Event's User Minigame ID
     * @param  string Event's User Minigame Unique ID
     *
     * @throws Symfony\Component\Debug\Exception\FatalThrowableError
     *
     * @return array
     */
	
	public function output($eventDays, $eventminiGameUniqueId = ""){

		if ($eventminiGameUniqueId) {
			$eventDays = $eventDays
				->where(['_id'=> $eventDays->_id, 'mini_games._id'=> $eventminiGameUniqueId])
				->project(['_id'=> true, 'events_user_id'=> true, 'from'=> true, 'to'=> true, 'mini_games.$'=> true])
				->get()
				->toArray();
		}

		if (!is_array($eventDays)) {
			$eventDays = $eventDays->makeHidden(['created_at', 'updated_at'])->toArray();
		}

		foreach ($eventDays as $key1 => &$day) {
			foreach ($day['mini_games'] as $key2 => &$game) {
				$game['_id'] = (string)$game['_id'];
				foreach ($game['completions'] as $key3 => &$completedData) {
					$completedData['completed_at'] = $completedData['completed_at']->toDateTime()->format('Y-m-d H:i:s');
				}
			}
		}

		return $eventDays;
	}

	public function filter($eventUsersMiniGames)
	{
		return $eventUsersMiniGames
				->where('from', '>=', today())
				->where('to', '<=', today()->endOfDay())
				->makeHidden(['created_at', 'updated_at'])
				->toArray();
	}
}