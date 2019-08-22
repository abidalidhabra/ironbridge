<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\GetEventsInCityRequest;
use App\Http\Requests\v2\MarkTheEventMGAsCompleteRequest;
use App\Http\Requests\v2\ParticipateInEventRequest;
use App\Models\v1\City;
use App\Models\v2\Event;
use App\Refacing\RefaceJustJoinedEvent;
use App\Repositories\EventRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

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
        try {
            
            return response()->json(['message'=> 'OK.', 'data'=> $this->eventRepo->create($request, new RefaceJustJoinedEvent)]);
        } catch (Exception $e) {
            return $e->getMessage().' on '.$e->getLine().' of '.$e->getFile();   
        }
    }

    public function markTheEventMGAsComplete(Request $request)
    {
        try {
            
            return response()->json(['message'=> 'OK.', 'data'=> $this->eventRepo->markMGAsComplete($request)]);
        } catch (Exception $e) {
            return $e->getMessage().' on '.$e->getLine().' of '.$e->getFile();   
        }
    }
}
