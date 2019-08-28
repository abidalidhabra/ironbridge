<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\GetEventsInCityRequest;
use App\Models\v1\City;
use App\Models\v2\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function getEventsCities(Request $request)
    {
        $cities = City::select('_id','name')->havingActiveEvents()->get();

        return ResponseHelpers::successResponse($cities);
    }

    public function getEventsInCity(GetEventsInCityRequest $request)
    {
        $events = Event::soonActivatedOrParticipated(auth()->user()->id)
                    ->havingCity($request->city_id)
                    ->withWinningPrize()
                    ->withParticipation($this->user->id)
                    ->select('_id', 'name', 'fees', 'description', 'starts_at', 'ends_at', 'discount', 'discount_amount', 'city_id', 'play_countdown', 'discount_countdown', 'status')
                    ->get();
                    
        return ResponseHelpers::successResponse($events);
    }
}
