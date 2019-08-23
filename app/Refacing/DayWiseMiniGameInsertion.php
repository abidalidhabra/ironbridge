<?php

namespace App\Refacing;

use App\Refacing\PriorToInsertRefaceable;

class DayWiseMiniGameInsertion implements PriorToInsertRefaceable {
	
	public function prepareToInsert($eventDays){

		foreach ($eventDays as &$day) {
			foreach ($day['mini_games'] as &$game) {
				$game['completions'] = [];
			}
		}

		return $eventDays;
	}
}