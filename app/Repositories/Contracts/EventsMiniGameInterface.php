<?php

namespace App\Repositories\Contracts;

interface EventsMiniGameInterface {
	
	public function createByEventsUser($eventUser, $data);

	public function addCompletion($id, $eventMiniGameUniqueId, $dataToPush);
	
	public function getStatus($id);
}