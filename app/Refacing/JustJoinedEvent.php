<?php

namespace App\Refacing;

use App\Refacing\LastFinishedEventRoundInterface;
use App\Refacing\PriorToInsertRefaceable;
use App\Refacing\Refaceable;
use App\Refacing\TodaysMinigameInteface;
use Carbon\Carbon;

class JustJoinedEvent implements PriorToInsertRefaceable, TodaysMinigameInteface, Refaceable, LastFinishedEventRoundInterface {
	
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
				->where('from', '<=', now())
				->where('to', '>=', now())
				->map(function($dayWiseMiniGames){
					// $dayWiseMiniGames = $this->getEventsUserMiniGamesStatus($dayWiseMiniGames);
					return collect($dayWiseMiniGames)->except(['created_at', 'updated_at']);
				})
				->values()
				->toArray();
	}

	public function lastFinishedMiniGames($eventUsersMiniGames)
	{
		return collect($eventUsersMiniGames)
			->where('from', '<=', now())
			->where('to', '<=', now())
			->map(function($dayWiseMiniGames){
				// $dayWiseMiniGames = $this->getEventsUserMiniGamesStatus($dayWiseMiniGames);
				return collect($dayWiseMiniGames)->except(['created_at', 'updated_at']);
			})
			->values()
			->toArray();
	}

	public function output(array $eventUsersMiniGames){

		$countableUserMiniGames = $this->todaysMiniGames($eventUsersMiniGames);

		if (!$countableUserMiniGames) {
			$countableUserMiniGames = $this->lastFinishedMiniGames($eventUsersMiniGames);
		}
		foreach ($countableUserMiniGames as $key1 => &$day) {
			foreach ($day['mini_games'] as $key2 => &$game) {
				$game['_id'] = $game['_id']->__toString();
				foreach ($game['completions'] as $key3 => &$completedData) {
					$completedData['_id'] = $completedData['_id']->__toString();
					$completedData['completed_at'] = $completedData['completed_at']->toDateTime()->format('Y-m-d H:i:s');
				}
			}
		}
		return $countableUserMiniGames;
	}

	// public function getEventsUserMiniGamesStatus($dayWiseMiniGames)
	// {

	// 	// $dayWiseMiniGames['mini_games_countdown'] = ($dayWiseMiniGames['from'] > now())? Carbon::parse($dayWiseMiniGames['from'])->diffInSeconds(): 0;

	// 	if ($dayWiseMiniGames['from'] <= now() && $dayWiseMiniGames['to'] >= now()) {
	// 		$dayWiseMiniGames['status'] = 'running';
	// 	}else{
	// 		$dayWiseMiniGames['status'] = 'closed';
	// 	}

	// 	return $dayWiseMiniGames;
	// }
}