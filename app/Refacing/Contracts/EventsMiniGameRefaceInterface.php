<?php

namespace App\Refacing\Contracts;

interface EventsMiniGameRefaceInterface {
	
	public function prepareToInsert(array $eventDays);

	public function todaysMiniGames(array $eventUsersMiniGames);

	public function lastFinishedMiniGames(array $eventUsersMiniGames);

	public function output(array $eventUsersMiniGames);

	public function prepareCompletionDataToInsert(array $requestData);

	public function outputInsertedCompletionData(array $preparedCompletionData);
}