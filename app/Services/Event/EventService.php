<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
use App\Models\v3\EventUser;
use App\Services\Traits\UserTraits;
use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;

class EventService
{
	use UserTraits;

	public $events;
	public $cities;
	public $users;
	public $insertedUsers;

	public function cities()
	{
		$this->cities = City::get();
	}

	public function events()
	{
		$this->events = $this->cities->pluck('events')->flatten();

	}

	public function users()
	{
		$this->users = User::whereIn('city_id', $this->cities->pluck('_id')->toArray())
						->whereNotNull('dob')
						->where('dob', '<', new UTCDateTime(now()->subYears(18)))
						// ->doesntHave('events', function($query){
						// 	$query->whereIn('event_id', $this->events->pluck('_id')->toArray());
						// })
						->select('_id', 'city_id')
						->get();
	}

	public function store()
	{
		$this->cities->each(function($city){
			$users = $this->users->where('city_id',$city->id);
			$city->events->each(function($event) use ($users, $city) {
				print_r($event);
				$dataToBeCreate = collect();
				$users->each(function($user) use ($event, &$dataToBeCreate) {
					// $user->events()->create(['event_id'=> $event->id, 'status'=> 'running']);
					$dataToBeCreate->push([
						'user_id'=> $user->id, 
						'event_id'=> $event->id, 
						'status'=> 'participated', 
						'radius'=> $event->total_radius,
						'compasses'=> [
							'utilized'=> 0,
							'remaining'=> 0
						],
						'created_at'=> new UTCDateTime,
						'updated_at'=> new UTCDateTime
					]);
				});
				print_r($dataToBeCreate);
				$this->insertedUsers = $dataToBeCreate->count();
				if ($dataToBeCreate->count()) {
					//EventUser::insert($dataToBeCreate->toArray());
				}
				$this->markAsStarted($event, $city);
			});exit;
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
		return $this;
	}

	public function finish()
	{
		$events = Event::finished()->get()->pluck('_id');
		Event::whereIn('_id', $events->toArray())->update([
			'finished_at'=> new UTCDateTime
		]);
	}


	public function participateMeInEventIfAny($user, $cityId)
	{
		$event = Event::running()->where('city_id', $cityId)->orderBy('time.start', 'asc')->first();
		if ($event) {
			EventUser::create([
				'user_id'=> $user->id, 
				'event_id'=> $event->id, 
				'status'=> 'participated',
				'radius'=> $event->total_radius,
				'compasses'=> [
					'utilized'=> 0,
					'remaining'=> 0
				],
				'created_at'=> new UTCDateTime,
				'updated_at'=> new UTCDateTime
			]);
		}
		return $event;
	}

	public function response()
	{
		return [
			'cities'=> $this->cities->count(),
			'events'=> $this->events->count(),
			'users'=> $this->users->count(),
			'inserted_users'=> $this->insertedUsers
		];
	}
}