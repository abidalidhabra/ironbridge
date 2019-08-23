<?php

namespace App\Refacing;

use App\Refacing\JustJoinedEventInterface;

// class JustJoinedEvent implements JustJoinedEventInterface {
class JustJoinedEvent implements PriorToInsertRefaceable, Filterfable, Refaceable {
	
	public function prepareToInsert($eventDays){

		foreach ($eventDays as &$day) {
			foreach ($day['mini_games'] as &$game) {
				$game['completions'] = [];
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

	public function output($eventUsersMiniGames){

		$eventUsersMiniGames = $this->filter($eventUsersMiniGames);

		foreach ($eventUsersMiniGames as $key1 => &$day) {
			foreach ($day['mini_games'] as $key2 => &$game) {
				$game['_id'] = (string)$game['_id'];
				foreach ($game['completions'] as $key3 => &$completedData) {
					$completedData['completed_at'] = $completedData['completed_at']->toDateTime()->format('Y-m-d H:i:s');
				}
			}
		}

		return $eventUsersMiniGames;
	}
}