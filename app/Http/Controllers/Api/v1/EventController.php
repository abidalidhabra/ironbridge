<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\Models\v1\EventLevel;
use App\Models\v1\EventParticipation;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\Models\v1\Event;
use Exception;
use UserHelper;
use App\Models\v1\City;
use App\Models\v1\ParticipantHistory;

class EventController extends Controller
{
    
    public function addParticipation(Request $request)
    {

    	/* Validation Stuff */
    	$validator = Validator::make($request->all(),[
    		'event_id'=>'required|exists:events,_id',
    	]);

    	if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first(),'amount'=>0], 422);
        }

    	/* Get the required variables */
    	$user = Auth::user();
    	$eventId = $request->get('event_id');

    	/* Lets take an event **/
    	$event = Event::find($eventId);

    	/* Check If user have enough coins to participate in event. */
    	if ($event->entry_fees > $user->gold_balance) {
    		return response()->json(['message' => "You don't have enough coins to participate in this event.",'amount'=>0],500);
    	}

        /* Check If user already participated in this event already. */
        if ($user->event_participations()->where('event_id',$eventId)->count()) {
            return response()->json(['message' => 'You are already participated in this event.','amount'=>0],500);
        }

        $user->balance_sheet()->create([
            'happens_at'        => 'EVENT_PARTICIPATION',
            'happens_because'   => 'EVENT_PARTICIPATION',
            'balance_type'      => 'DR',
            'debit'            => $event->entry_fees,
        ]);

        /** Add the gold in user's account **/
        $user->decrement('gold_balance',$event->entry_fees);

    	/* Participate the user into this event. */
    	if ($user->event_participations()->save(new EventParticipation(['event_id' => $eventId]))) {
    		return response()->json(['message' => 'You have successfully participated into an event.','amount'=>$user->gold_balance],200);
    	}else{
    		return response()->json(['message' => 'Something went wrong while participating into an event.','amount'=>0],500);
    	}
    }

    public function getTheEvents(Request $request)
    {

    	/* Validation Stuff */
    	$validator = Validator::make($request->all(),[
    		'city_id'=>'nullable|exists:cities,_id',
    		'page'=>'required|numeric|min:1',
    	]);

    	if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

    	/* Get the required variables */
    	$user = Auth::user();
    	$page = $request->get('page');
    	$take = 500;
    	$skip = $page * $take;
    	$cityId = $request->get('city_id');
        $now = new MongoDate(Carbon::now());

    	if (!$cityId && $user->address) {
    		$city = City::where('name', 'like','%'.$user->address['city'].'%')->first();
    		if ($city) {
    			$cityId = $city->id;
    		}
    	}else if($cityId){
            $city = City::find($cityId);
        }

        /** Get the events which which are started soon or which are not ends yet. **/
        $eventsToShow = Event::whereNotNull('activated_at')
                        ->where('practice_event',false)
                        ->when($cityId, function($query, $cityId){
                            return $query->where('city_id', $cityId);
                        })
                        ->orWhere(function($query) use ($now){
                            $query->where('starts_at', '>=', $now);
                            $query->where('ends_at', '>=', $now);
                        })
                        ->orWhere(function($query) use ($now){
                            $query->where('starts_at', '<=', $now);
                            $query->where('ends_at', '>=', $now);
                        });

        /** Get the data with events. **/
        // \DB::connection()->enableQueryLog();
        $events = $eventsToShow->with(['city'=> function($query){
                        $query->with('country:_id,name')->select('_id','name','country_id','timezone');
                    },'event_pricemoney','event_levels' => function($query){
                        $query->with(['game_variation' => function($query){
                                $query->with('game');
                            }])
                            ->orderBy('level','asc')
                            ->select('_id','event_id','game_variation_id','level','target','starts_at','ends_at');
                    },'event_participations' => function($query) use ($user) {
                        $query->where('user_id',$user->id)->select('_id','event_id','user_id','completed_levels');
                    }])
                    ->select('_id','city_id','title','starts_at','ends_at')
                    ->paginate($take,['*'],'page',$page);
                    $queries = \DB::getQueryLog();
        // dd($queries);
        /** Prepare flags for an events **/
        $events = UserHelper::addRequiredFlags($events);

    	if ($events->total()) {
    		return response()->json([
                'message' => 'Events has been retrieved successfully.', 
                'data' => [
                    'events' => $events->all(),
                    'city'   => $city,
                ]
            ],200);
    	}else{
    		return response()->json([
                'message' => 'Sorry! Not a single active event found around your city.', 
                'data' => []
            ],422);
    	}
    }

    public function hitAnEventAction(Request $request)
    {
        
        /* Validation Stuff */
        $validator = Validator::make($request->all(),[
            'event_participation_id' => 'required|exists:event_participations,_id',
            'event_level_id' => 'required|exists:event_levels,_id',
            'level' => 'required|numeric',
            'duration' => 'required|numeric',
            'status' => 'required|in:FAILED,COMPLETED',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

        $eventParticipationId = $request->get('event_participation_id');
        $eventLevelId = $request->get('event_level_id');
        $duration = $request->get('duration');
        $status = $request->get('status');
        $level = $request->get('level');

        $eventParticipation = EventParticipation::find($eventParticipationId);

        if (!$eventParticipation) {
            return response()->json(['message' => 'You are not authorized to process this request.'],500);
        }

        if ($status == 'COMPLETED') {
            $eventParticipation->push('completed_levels',$level,true);
            // ParticipantHistory::where('_id',$eventParticipationId)->push('completed_levels',$level,true);
        }

        $eventParticipation->history()->save( new ParticipantHistory([
            'event_level_id' => $eventLevelId,
            'duration' => $duration,
            'status' => $status,
        ]));

        return response()->json(['message' => 'Event action has been taken successfully.'],200);
    }
}
