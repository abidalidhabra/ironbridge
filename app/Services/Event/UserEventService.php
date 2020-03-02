<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
use App\Services\Traits\UserTraits;
use DateInterval;
use MongoDB\BSON\UTCDateTime;
use DateTimeImmutable;

class UserEventService
{
	use UserTraits;

	public $event;
	public $earnedCompasses;
	
	public function running(array $fields = ['*'])
	{
		$this->event = Event::whereHas('participations', function($query){
							$query->where('user_id', $this->user->id);
						})
						->running()
						->first($fields);
		return $this->event;
	}

	public function totalEarnedCompasses()
	{
		if (!$this->event) {
			$this->running();
		}
		$this->earnedCompasses = $this->user->assets()->compasses()->where('event_id', $this->event->id)->sum('compasses');
		return $this->earnedCompasses;
	}

	public function thisWeekEarnedCompasses()
	{
		if (!$this->event) {
			$this->running();
		}

		$startDate = new DateTimeImmutable($this->event->getOriginal('time')['start']->toDateTime()->format('Y-m-d H:i:s'));
		$weekLater = $startDate->add(new DateInterval('P7D'));
		$this->thisWeekEarnedCompasses = $this->user->assets()->compasses()
										->where('event_id', $this->event->id)
										->where('created_at', '>=', new UTCDateTime($startDate))
										->where('created_at', '<=', new UTCDateTime($weekLater))
										->sum('compasses');
		return $this->thisWeekEarnedCompasses;
	}
}