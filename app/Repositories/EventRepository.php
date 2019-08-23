<?php

namespace App\Repositories;

use App\Helpers\ExceptionHelpers;
use App\Models\v1\City;
use App\Models\v2\Event;
use App\Models\v2\EventsMinigame;
use App\Models\v2\EventsUser;
use App\Refacing\JustJoinedEvent;
use App\Refacing\MarkEventMGAsComplete;
use App\Repositories\User\UserRepository;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Throwable;

class EventRepository
{
    private $user;
    private $userRepo;

    public function __construct($user)
    {
        $this->user = $user;
        $this->userRepo = new UserRepository($user);
    }

	public function cities()
    {

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
                    ->select('_id', 'name', 'fees', 'description', 'starts_at', 'ends_at', 'discount', 'discount_amount', 'city_id', 'play_countdown', 'discount_countdown', 'status')
                    ->get();
                    
        return $events;
    }

    public function create($eventData)
    {
        try {

            $justJoinedEvent = new JustJoinedEvent();

            $event = Event::find($eventData->event_id);

            // shot the event user data into the database
            $eventUser = $this->addTheEventParticipation($event);
            // $eventUser->delete();

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
                    'event'=> $event->only('_id', 'name', 'discount_countdown', 'play_countdown', 'status'),
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

    function addTheEventParticipation($event)
    {
        return $this->user->events()->create(['event_id'=> $event->_id, 'attempts'=> $event->attempts]);
    }

    function addTheDayWiseMiniGames($eventUser, $eventUserMiniGame)
    {
        return $eventUser->minigames()->createMany($eventUserMiniGame);
    }

    public function deductTheCoins($condsToBeDeduct)
    {
        return $this->userRepo->deductTheCoins($condsToBeDeduct);
    }

    public function findMiniGameById($miniGameId)
    {
        return EventsMinigame::where(['_id'=> $miniGameId])->firstOrFail();
    }

    public function findEventsUserById($eventsUserId)
    {
        return EventsUser::where(['_id'=> $eventsUserId])->firstOrFail();
    }

    public function addMiniGameCompletion($fields, $dataToPush)
    {
        return EventsMinigame::where($fields)->push('mini_games.$.completions', $dataToPush);
    }
    public function markMiniGameAsComplete($miniGameData)
    {
        try {

            $markEventMGAsComplete = new MarkEventMGAsComplete();

            /** prepare the data prior to insert **/
            $insertableData = $markEventMGAsComplete->prepareToInsert($miniGameData->all());
            
            /** shot into the database **/
            EventsMinigame::where(['mini_games._id'=> $miniGameData->minigame_unique_id])->push('mini_games.$.completions', $insertableData);
            $eventsMinigame = EventsMinigame::where(['_id'=> $miniGameData->events_minigame_id, 'mini_games._id'=> $miniGameData->minigame_unique_id])->select('_id', 'from', 'to', 'status')->first();
            // $this->addMiniGameCompletion(['_id'=> $miniGameData->events_minigame_id, 'mini_games._id'=> $miniGameData->minigame_unique_id], $insertableData);

            /** prepare output for the client **/
            $insertedData = $markEventMGAsComplete->output(array_merge($insertableData, ['status'=> $eventsMinigame->status], $miniGameData->only('events_minigame_id', 'minigame_unique_id')));

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

    public function getPresentDayEventDetail($requestedData)
    {

        $eventUser = $this->findEventsUserById($requestedData->events_user_id);
        
        $event = $eventUser->event()
                    ->select('_id', 'name', 'discount_till', 'discount_countdown', 'starts_at', 'play_countdown', 'status')
                    ->first();
        
        $justJoinedEvent = new justJoinedEvent();
        $eventUserMiniGames = $justJoinedEvent->output($eventUser->minigames()->get()->toArray());

        return response()->json([
                'message'=>'OK', 'data'=> [ 'event'=> $event, 'event_user'=> $eventUser, 'event_user_minigames'=> $eventUserMiniGames ]
            ]);
    }
}