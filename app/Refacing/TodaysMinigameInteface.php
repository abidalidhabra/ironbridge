<?php

namespace App\Refacing;

interface TodaysMinigameInteface {

	/**
     * Return only todays event round.
     *
     * @param  array  $eventUsersMiniGames
     *
     * @return array
     */
	public function todaysMiniGames($eventUsersMiniGames);
}