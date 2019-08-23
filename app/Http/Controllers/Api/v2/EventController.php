<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\GetEventsInCityRequest;
use App\Http\Requests\v2\MarkTheEventMGAsCompleteRequest;
use App\Http\Requests\v2\ParticipateInEventRequest;
use App\Repositories\EventRepository;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $eventRepo;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->eventRepo = new EventRepository(auth()->user());
            return $next($request);
        });
    }

    public function getEventsCities(Request $request)
    {
        return response()->json(['message'=> 'OK.', 'data'=> $this->eventRepo->cities()]);
    }

    public function getEventsInCity(GetEventsInCityRequest $request)
    {
        return response()->json(['message'=> 'OK.', 'data'=> $this->eventRepo->eventsInCity($request->city_id)]);
    }

    public function participateInEvent(ParticipateInEventRequest $request)
    {
        return  $this->eventRepo->create($request);
    }

    public function markTheEventMGAsComplete(MarkTheEventMGAsCompleteRequest $request)
    {
        return $this->eventRepo->markMGAsComplete($request);
    }
}
