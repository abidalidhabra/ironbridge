<?php

namespace App\Repositories;

use App\Models\v2\EventsUser;
use App\Repositories\Contracts\EventsUserInterface;

class EventsUserRepository implements EventsUserInterface
{

	public function find($id, $fields = ['*'])
	{
		return EventsUser::find($id, $fields);
	}

	function createByUser($user, $event)
	{
        return $user->events()->create(['event_id'=> $event->_id, 'attempts'=> $event->attempts]);
	}

	public function event(EventsUser $eventsUser, $fields = ['*'])
	{
		return $eventsUser->event()->select($fields)->first();
	}

	public function miniGames(EventsUser $eventsUser, $fields = ['*'])
	{
		return $eventsUser->minigames()->select($fields)->get();
	}
}