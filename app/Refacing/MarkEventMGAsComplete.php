<?php

namespace App\Refacing;

use App\Refacing\Refaceable;
use App\Refacing\PriorToInsertRefaceable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class MarkEventMGAsComplete implements PriorToInsertRefaceable, Refaceable  {

	public function prepareToInsert(array $miniGameData){

		$completedAt = new UTCDateTime();

		if (isset($miniGameData['completion_score'])) {
			$completionScore = (int)$miniGameData['completion_score'];
		}

		if (isset($miniGameData['completion_time'])) {
			$completionTime = (int)$miniGameData['completion_time'];
		}
		
		return [
			'_id'=> new ObjectId(), 
			'completed_at'=> $completedAt, 
			'completion_score'=> $completionScore ?? null, 
			'completion_time'=> $completionTime ?? null
		];
	}

	public function output(array $completionData)
	{

		return [
			'_id'=> $completionData['_id']->__toString(), 
			'completed_at'=> $completionData['completed_at']->toDateTime()->format('Y-m-d H:i:s'), 
			'completion_score'=> $completionData['completion_score'],
			'completion_time'=> $completionData['completion_time'],
			'event_minigame_id'=> $completionData['event_minigame_id'],
			'minigame_unique_id'=> $completionData['minigame_unique_id'],
			'status'=> $completionData['status']
		];
	}
}