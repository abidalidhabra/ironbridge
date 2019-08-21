<?php

namespace App\Repositories;

use App\Models\v1\City;
use App\Models\v2\Event;
use Carbon\Carbon;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class EventRepository
{
    // private $user;

    // public function __construct($user = null)
    // {
    //     $this->user = ($user)?: auth()->user();
    // }

	public function cities(){

        $cities = City::select('_id','name')->havingActiveEvents()->get();
        return $cities;
	}

    public function eventsInCity($cityId)
    {
        $events = Event::upcoming()->havingCity($cityId)
                    ->with('prizes:_id,event_id,group_type,prize_type,prize_value,rank')
                    ->with(['participants'=> function($query){
                        $query->where('user_id', auth()->user()->id)->select('_id', 'user_id');
                    }])
                    ->select('_id','name','fees','description','starts_at','ends_at','discount','city_id')
                    ->get()
                    ->map(function($event){ 
                        $event->play_countdown = ($event->starts_at > now())? $event->starts_at->diffInSeconds() : 0;
                        $event->discount_countdown = ($event->discount_till > now())? $event->discount_till->diffInSeconds() : 0;
                        return $event;
                    });
        return $events;
    }

    public function create($eventData)
    {
        return auth()->user()->events()->create(['event_id'=> $eventData->event_id]);
    }
}