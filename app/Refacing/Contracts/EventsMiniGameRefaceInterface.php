<?php

namespace App\Refacing\Contracts;

use App\Models\v2\Event;
use Illuminate\Support\Collection;

interface EventsMiniGameRefaceInterface {
	
	public function prepareToInsert(array $eventDays);

	public function firstRoundMiniGames(array $eventUsersMiniGames);

	public function todaysRoundMiniGames(array $eventUsersMiniGames);

	public function lastFinishedRoundMiniGames(array $eventUsersMiniGames);

	// public function output(array $eventUsersMiniGames);

	public function output(Event $event, Collection $eventMinigame);

	public function prepareCompletionDataToInsert(array $requestData);

	public function outputInsertedCompletionData(array $preparedCompletionData);
}