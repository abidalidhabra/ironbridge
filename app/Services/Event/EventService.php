<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
use App\Services\Traits\UserTraits;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use DateTimeZone;

class EventService
{
	use UserTraits;

	public $events;
	public $cities;
	public $users;

	public function cities()
	{
		$this->cities = City::whereHas('events', function($query){
					$query->participateable()->limit(1);
				})->with(['events'=> function($query){
					$query->participateable()->limit(1);
				}])
				->get();
	}

	public function events()
	{
		$this->events = $this->cities->pluck('events')->flatten();
	}

	public function users()
	{
		$this->users = User::whereIn('city_id', $this->cities->pluck('_id')->toArray())
						->doesntHave('events', function($query){
							$query->whereIn('event_id', $this->events->pluck('_id')->toArray());
						})
						->select('_id', 'city_id')
						->get();
	}

	public function store()
	{
		$this->cities->each(function($city){
			$users = $this->users->where('city_id',$city->id);
			$city->events->each(function($event) use ($users, $city) {
				$users->each(function($user) use ($event) {
					$user->events()->create(['event_id'=> $event->id, 'status'=> 'running']);
				});
				$this->markAsStarted($event, $city);
			});
		});
	}

	public function markAsStarted($event, $city)
	{
		$localDate = new DateTime(now(), new DateTimeZone($city->timezone));
        $UTCDate = $localDate->setTimezone(new DateTimeZone('UTC'));
        $UTCDateTime = new UTCDateTime($UTCDate->format('U') * 1000);
		$event->started_at = $UTCDateTime;
		$event->save();
	}

	public function participate()
	{
		$this->cities();
		$this->events();
		$this->users();
		$this->store();
	}

	public function finish()
	{
		Event::finished()->get()->each(function($event){
			$event->finished_at = new UTCDateTime;
			$event->save();
		});
	}
}