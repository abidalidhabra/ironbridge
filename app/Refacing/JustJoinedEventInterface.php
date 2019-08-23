<?php

namespace App\Refacing;

interface JustJoinedEventInterface {

	public function prepareToInsert($eventUsersMiniGames);

	public function filter($eventUsersMiniGames);
	
	public function output($eventUsersMiniGames);
}