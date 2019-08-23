<?php

namespace App\Repositories;

use App\Helpers\ExceptionHelpers;
use App\Models\v1\City;
use App\Models\v2\Event;
use App\Models\v2\EventsMinigame;
use App\Refacing\JustJoinedEvent;
use App\Refacing\MarkEventMGAsComplete;
use App\Repositories\User\UserRepository;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Throwable;
use Exception;

class EventRepository
{
    private $user;
    private $userRepo;

    public function __construct($user)
    {
        $this->user = $user;
        $this->userRepo = new UserRepository($user);
    }

	public function cities(){

        $cities = City::select('_id','name')->havingActiveEvents()->get();
        return $cities;
	}

    public function eventsInCity($cityId)
    {
        $events = Event::upcoming()->havingCity($cityId)
                    ->with(['prizes'=> function($query) {
                        $query->where(function($query){
                            $query->orWhere('rank', 1)->orWhere('start_rank', 1);
                        })
                        ->select('_id','event_id','group_type','prize_type','prize_value','rank', 'start_rank', 'end_rank');
                    }])
                    ->with(['participations'=> function($query){
                        $query->where('user_id', $this->user->id)->select('_id', 'event_id', 'user_id', 'status');
                    }])
                    ->select('_id','name','fees','description','starts_at','ends_at','discount','discount_amount','city_id')
                    ->get()
                    ->map(function($event){ 
                        $event->play_countdown = ($event->starts_at > now())? $event->starts_at->diffInSeconds(): 0;
                        $event->discount_countdown = ($event->discount_till > now())? $event->discount_till->diffInSeconds(): 0;
                        return $event;
                    });
        return $events;
    }

    public function create($eventData)
    {
        try {

            $justJoinedEvent = new JustJoinedEvent();

            $event = Event::find($eventData->event_id);

            // shot the event user data into the database
            $eventUser = $this->addTheEventParticipation($event);
            $eventUser->delete();

            // prepare the data prior to insert
            $eventUserMiniGame = $justJoinedEvent->prepareToInsert($event->event_days);

            // shot minigame data into the database
            $eventUserMG = $this->addTheDayWiseMiniGames($eventUser, $eventUserMiniGame);

            //deduct the coins
            $availableGold = $this->deductTheCoins($event->fees);

            // prepare output for the client
            $eventUserMG = $justJoinedEvent->output($eventUserMG->toArray());

            return response()->json([
                'message'=>'OK', 
                'data'=> [
                    'event_user'=> $eventUser, 
                    'event_user_minigames'=> $eventUserMG, 
                    'available_gold'=> $availableGold
                ]
            ]);
            
        } catch (Throwable $e) {
            return ExceptionHelpers::getResourceResponse($e);
        } catch (Exception $e) {
            return ExceptionHelpers::getResourceResponse($e);
        }
    }

    public function find($eventId)
    {
        return Event::find($eventId);
    }

    function addTheEventParticipation($event){
        return $this->user->events()->create(['event_id'=> $event->_id, 'attempts'=> $event->attempts]);
    }

    function addTheDayWiseMiniGames($eventUser, $eventUserMiniGame){
        return $eventUser->minigames()->createMany($eventUserMiniGame);
    }

    public function deductTheCoins($condsToBeDeduct)
    {
        return $this->userRepo->deductTheCoins($condsToBeDeduct);
    }

    public function findMGById($miniGameId)
    {
        return EventsMinigame::where(['_id'=> $miniGameId])->firstOrFail();
    }

    public function addMGCompletion($fields, $dataToPush)
    {
        return EventsMinigame::where($fields)->push('mini_games.$.completions', $dataToPush);
    }
    public function markMGAsComplete($miniGameData)
    {
        try {

            $markEventMGAsComplete = new MarkEventMGAsComplete();

            /** prepare the data prior to insert **/
            $insertableData = $markEventMGAsComplete->prepareToInsert($miniGameData->all());
            
            /** shot into the database **/
            $this->addMGCompletion(['_id'=> $miniGameData->event_minigame_id, 'mini_games._id'=> $miniGameData->minigame_unique_id], $insertableData);

            /** prepare output for the client **/
            $insertedData = $markEventMGAsComplete->output(array_merge($insertableData, $miniGameData->only('event_minigame_id', 'minigame_unique_id')));

            return $insertedData;
        } catch (Throwable $e) {
            return ExceptionHelpers::getResourceResponse($e);
        } catch (Exception $e) {
            return ExceptionHelpers::getResourceResponse($e);
        }
    }

    public function getTodayMiniGame($eventMiniGameId)
    {
        return EventsMinigame::where('from', '>=', new UTCDateTime(today()))
                ->where('to', '<=', new UTCDateTime(today()->endOfDay()))
                ->first();
    }
}