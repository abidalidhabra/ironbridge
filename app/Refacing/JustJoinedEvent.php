<?php

namespace App\Refacing;

use App\Refacing\PriorToInsertRefaceable;
use App\Refacing\Refaceable;
use App\Refacing\TodaysMinigameInteface;

class JustJoinedEvent implements PriorToInsertRefaceable, TodaysMinigameInteface, Refaceable {
	
	public function prepareToInsert(array $eventDays){

		foreach ($eventDays as &$day) {
			foreach ($day['mini_games'] as &$game) {
				$game['completions'] = [];
			}
		}
		return $eventDays;
	}

	public function todaysMiniGames($eventUsersMiniGames)
	{
		return collect($eventUsersMiniGames)
				->where('from', '>=', today())
				->where('to', '<=', today()->endOfDay())
				->map(function($dayWiseMiniGames){
					return collect($dayWiseMiniGames)->except(['created_at', 'updated_at']);
				})
				->toArray();
	}

	public function output($eventUsersMiniGames){

		$eventUsersMiniGames = $this->todaysMiniGames($eventUsersMiniGames);

		foreach ($eventUsersMiniGames as $key1 => &$day) {
			foreach ($day['mini_games'] as $key2 => &$game) {
				// $game['_id'] = (string)$game['_id'];
				$game['_id'] = $game['_id']->__toString();
				foreach ($game['completions'] as $key3 => &$completedData) {
					$completedData['completed_at'] = $completedData['completed_at']->toDateTime()->format('Y-m-d H:i:s');
				}
			}
		}

		return $eventUsersMiniGames;
	}
}