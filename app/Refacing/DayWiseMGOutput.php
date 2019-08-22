<?php

namespace App\Refacing;

use App\Refacing\DayWiseMGOutputInterface;

class DayWiseMGOutput implements DayWiseMGOutputInterface {

	public function output($dayWiseGames, $gameUniqueId = ""){

		if ($gameUniqueId) {
			$dayWiseGames = $dayWiseGames
				->where(['games._id'=> $gameUniqueId])
				->project(['from'=> true, 'to'=> true, 'games.$'=> true])
				->firstOrFail()
				->toArray();
		}

		if (!is_array($dayWiseGames)) {
			$dayWiseGames = $dayWiseGames->toArray();
		}

		foreach ($dayWiseGames['games'] as $key => &$game) {
			$game['_id'] = (string)$game['_id'];
			foreach ($game['completions'] as $key => &$completedData) {
				$completedData['completed_at'] = $completedData['completed_at']->toDateTime()->format('Y-m-d H:i:s');
			}
		}

		return $dayWiseGames;
	}
}