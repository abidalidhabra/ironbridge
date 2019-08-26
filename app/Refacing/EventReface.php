<?php

namespace App\Refacing;

use App\Models\v2\Event;
use App\Refacing\Contracts\EventRefaceInterface;

class EventReface implements EventRefaceInterface {

	public function output(Event $event) 
	{
		return $event->only('_id', 'name', 'discount_countdown', 'discount_till', 'play_countdown', 'starts_at', 'status');
	}
}