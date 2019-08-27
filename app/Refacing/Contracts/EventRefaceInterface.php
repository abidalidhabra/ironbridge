<?php

namespace App\Refacing\Contracts;

use App\Models\v2\Event;

interface EventRefaceInterface {
	
	// @param event instance return type event instance
	public function output(Event $event);

}