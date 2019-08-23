<?php

namespace App\Refacing;

interface LastFinishedEventRoundInterface {

	/**
     * Return last finished event round.
     *
     * @param  array  $eventUsersMiniGames
     *
     * @return array
     */
	public function lastFinishedMiniGames($eventUsersMiniGames);
}