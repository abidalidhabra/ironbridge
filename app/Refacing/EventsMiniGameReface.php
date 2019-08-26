<?php

namespace App\Refacing;

use App\Refacing\Contracts\EventsMiniGameRefaceInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class EventsMiniGameReface implements EventsMiniGameRefaceInterface {
	
	public function prepareToInsert(array $eventDays){

		foreach ($eventDays as &$day) {
			foreach ($day['mini_games'] as &$game) {
				$game['completions'] = [];
			}
		}
		return $eventDays;
	}

	public function todaysMiniGames(array $eventUsersMiniGames)
	{

		return collect($eventUsersMiniGames)
				->where('from', '<=', now())
				->where('to', '>=', now())
				->map(function($dayWiseMiniGames){
					return collect($dayWiseMiniGames)->except(['created_at', 'updated_at']);
				})
				->values()
				->toArray();
	}

	public function lastFinishedMiniGames(array $eventUsersMiniGames)
	{
		return collect($eventUsersMiniGames)
			->where('from', '<=', now())
			->where('to', '<=', now())
			->map(function($dayWiseMiniGames){
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

	public function prepareCompletionDataToInsert(array $requestData){

		$completedAt = new UTCDateTime();

		if (isset($requestData['completion_score'])) {
			$completionScore = (int)$requestData['completion_score'];
		}

		if (isset($requestData['completion_time'])) {
			$completionTime = (int)$requestData['completion_time'];
		}
		
		return [
			'_id'=> new ObjectId(), 
			'completed_at'=> $completedAt, 
			'completion_score'=> $completionScore ?? null, 
			'completion_time'=> $completionTime ?? null
		];
	}

	public function outputInsertedCompletionData(array $preparedCompletionData)
	{
		return [
			'_id'=> $preparedCompletionData['_id']->__toString(), 
			'completed_at'=> $preparedCompletionData['completed_at']->toDateTime()->format('Y-m-d H:i:s'), 
			'completion_score'=> $preparedCompletionData['completion_score'],
			'completion_time'=> $preparedCompletionData['completion_time'],
			'events_minigame_id'=> $preparedCompletionData['events_minigame_id'],
			'minigame_unique_id'=> $preparedCompletionData['minigame_unique_id'],
			'status'=> $preparedCompletionData['status']
		];
	}
}