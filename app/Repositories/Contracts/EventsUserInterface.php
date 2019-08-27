<?php

namespace App\Repositories\Contracts;

use App\Models\v2\EventsUser;

interface EventsUserInterface {
	
	public function find($id, $fields);

	public function createByUser($user, $event);

	public function event(EventsUser $eventsUser, $fields);
	
	public function miniGames(EventsUser $eventsUser, $fields);
}