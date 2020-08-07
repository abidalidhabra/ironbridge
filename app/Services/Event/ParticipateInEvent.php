<?php

namespace App\Services\Event;

use App\Models\v1\User;
use MongoDB\BSON\UTCDateTime;

class ParticipateInEvent
{
	
	public $users;
	
	public function users()
	{
		$this->users = User::whereHas('city', function($query){
				return $query->whereHas('events', function($query){
					return $query
					->whereNull('started_at')
					->where('time.start', '<=', new UTCDateTime(now()->getTimestamp() * 1000))
					->where('time.end', '>=', new UTCDateTime(now()->getTimestamp() * 1000));
				});
			})
			->with(['city'=> function($query){
				return $query->with(['events'=>function($query){
					return $query
					->whereNull('started_at')
					->where('time.start', '<=', new UTCDateTime(now()->getTimestamp() * 1000))
					->where('time.end', '>=', new UTCDateTime(now()->getTimestamp() * 1000))
					->select('_id', 'name', 'city_id')
					->first();
				}])
				->select('_id', 'name');
			}])
			->select('_id', 'city_id')
			->get();
	}

	public function events()
	{
		$this->users();
		$this->users->map(function($user){
			$user->city->events->map(function($event) use ($user){
				$this->participate($user, $event);
				if (!$event->started_at) {
					$this->markTheEventAsStart($event);
				}
			});
		});
	}

	public function participate($user, $event)
	{
		$user->events()->create([
			'event_id'=> $event->id
		]);
	}

	public function markTheEventAsStart($event)
	{
		$event->started_at = now();
		$event->save();
	}

	public function handle()
	{
		$this->events();
	}
}