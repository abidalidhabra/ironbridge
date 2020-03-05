<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
use App\Services\Event\EventUserService;
use App\Services\Traits\UserTraits;
use App\Services\User\CompassService;
use DateInterval;
use DateTimeImmutable;
use MongoDB\BSON\UTCDateTime;
use Exception;

class EventUserService
{
	use UserTraits;

	public $event;
	public $earnedCompasses;

	public function setEvent($event)
	{
		$this->event = $event;
		return $this;
	}
	
	public function running(array $fields = ['*'], $withParticipation = false)
	{
		$this->event = Event::whereHas('participations', function($query){
							$query->where('user_id', $this->user->id);
						})
						->when($withParticipation, function($query) {
							$query->with(['participations'=> function($query){
								$query->where('user_id', $this->user->id);
							}]);
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

		$dates = $this->getWeekDates();
		$this->thisWeekEarnedCompasses = $this->user->assets()->compasses()
										->where('event_id', $this->event->id)
										->where('created_at', '>=', new UTCDateTime($dates['start']))
										->where('created_at', '<=', new UTCDateTime($dates['end']))
										->sum('compasses');
		return $this->thisWeekEarnedCompasses;
	}

	public function getWeekDates()
	{

		$startDate = new DateTimeImmutable($this->event->getOriginal('time')['start']->toDateTime()->format('Y-m-d H:i:s'));
		$endDate = new DateTimeImmutable($this->event->getOriginal('time')['end']->toDateTime()->format('Y-m-d H:i:s'));

		$totalWeeks = ceil($endDate->diff($startDate)->days/7);

		$weekStartDate = $startDate;
		$weekEndDate = $weekStartDate->add(new DateInterval('P7D'));
		$intervals = [];
		for ($i=0; $i < $totalWeeks; $i++) { 
			$intervals[$i]['start'] = $weekStartDate;
			$intervals[$i]['end'] = $weekEndDate;

			$weekStartDate = $weekEndDate;
			$weekEndDate = $weekStartDate->add(new DateInterval('P7D'));
		}
		$now =  new DateTimeImmutable();
		$dates = collect($intervals)->where('start', '<=', $now)->where('end', '>', $now)->first();
		return $dates;
	}

	// public function utilizeTheCompasses()
	// {
	// 	if (!$this->event) {
	// 		$this->running();
	// 	}

	// 	$this->event->load(['participations'=> function($query) {
	// 		$query->where('user_id', $this->user->id);
	// 	}]);

	// 	$eventUser = $this->event->participations->first();
	// 	$eventUser->radius -= $this->event->deductable_radius;
	// 	$eventUser->save();

	// 	dd($this->event->participations->first());
	// }

	// public function reduceTheRadius()
	// {
	// 	// $validator = Validator::make($request->all(),[
	// 	// 	'cursor'=> 'required|integer',
	// 	// 	'direction'  => 'required|in:up,down',
	// 	// ]);

	// 	// if ($validator->fails()){
	// 	// 	return response()->json(['message' => $validator->messages()->first()]);
	// 	// }
	// 	try{
	// 	\DB::connection()->enableQueryLog();
	// 	$event = $this->running(['*'], true);
	// 	$eventUser = (new CompassService)
	// 				->setUser(auth()->user())
	// 				->setEvent($event)
	// 				->setEventUser($event->participations->first())
	// 				->deduct();
	// 	$queries = \DB::getQueryLog();
	// 	dd($eventUser->response());
	// 	} catch (Exception $e) {
 //            dd($e->getMessage());
 //        }
	// }
}