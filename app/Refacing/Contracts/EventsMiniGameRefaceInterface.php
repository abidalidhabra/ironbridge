<?php

namespace App\Refacing\Contracts;

use App\Models\v2\Event;
use Illuminate\Support\Collection;

interface EventsMiniGameRefaceInterface {
	
	public function prepareToInsert(array $eventDays);

	public function firstRoundMiniGames(Collection $eventUsersMiniGames);

	public function todaysRoundMiniGames(Collection $eventUsersMiniGames);

	public function lastFinishedRoundMiniGames(Collection $eventUsersMiniGames);

	// public function output(array $eventUsersMiniGames);

	public function output(Event $event, Collection $eventMinigame);

	public function prepareCompletionDataToInsert(array $requestData);

	public function outputInsertedCompletionData(array $preparedCompletionData);
}