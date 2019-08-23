<?php

namespace App\Refacing;

use App\Refacing\Refaceable;
use App\Refacing\PriorToInsertRefaceable;
use MongoDB\BSON\UTCDateTime;

class MarkEventMGAsComplete implements PriorToInsertRefaceable, Refaceable  {
	
	public function prepareToInsert($miniGameData, $eventminiGameUniqueId = ""){

		$completedAt = new UTCDateTime();

		if ($miniGameData->completion_score) {
			$completionScore = (int)$miniGameData->completion_score;
		}

		if ($miniGameData->completion_time) {
			$completionTime = (int)$miniGameData->completion_time;
		}
		return [
			'completed_at'=> $completedAt, 
			'completion_score'=> $completionScore ?? null, 
			'completion_time'=> $completionTime ?? null
		];
	}

	public function output($completionData)
	{
		return [
			'completed_at'=> $completionData['completed_at']->toDateTime()->format('Y-m-d H:i:s'), 
			'completion_score'=> $completionData['completion_score'], 
			'completion_time'=> $completionData['completion_time']
		];
	}
}