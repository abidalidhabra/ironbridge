<?php

namespace App\Repositories;

use App\Models\v1\City;
use App\Models\v2\Event;
use App\Models\v2\EventsMinigame;
use App\Refacing\DayWiseMGOutput;
use App\Refacing\DayWiseMGOutputInterface;
use App\Refacing\RefaceJustJoinedEvent;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

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

    public function create($eventData, RefaceJustJoinedEvent $refaceJustJoinedEvent)
    {

        $event = Event::find($eventData->event_id);

        // this is hunt user shot
        $eventUser = $this->addTheEventParticipation($event);
            $eventUser->delete();

        // this shot is for hunt user's minigames
        $eventUserMG = $this->addTheDayWiseMiniGames($event, $eventUser);

        //deduct the coins
        $availableGold = $this->deductTheCoins($event->fees);

        $eventUserMG = $refaceJustJoinedEvent->output($eventUserMG);
        return ['event_user'=> $eventUser, 'event_user_minigames'=> $eventUserMG, 'available_gold'=> $availableGold];
    }

    public function find($eventId)
    {
        return Event::find($eventId);
    }

    function addTheEventParticipation($event){
        return $this->user->events()->create(['event_id'=> $event->_id, 'attempts'=> $event->attempts]);
    }

    function addTheDayWiseMiniGames($event, $eventUser){

        $data = $event->event_days;
        foreach ($data as $key1 => &$day) {
            foreach ($day['mini_games'] as $key2 => &$game) {
                $game['completions'] = [];
            }
        }
        return $eventUser->minigames()->createMany($data);
    }

    // function addTheDayWiseMiniGames($event, $eventUser){

    //     $minigames = collect($event->mini_games)->map(function($day, $key1){
    //         $day['games'] = collect($day['games'])->map(function($game, $key2){
    //             $game['completions'] = [];
    //             return $game;
    //         })
    //         ->toArray();
    //         return $day;
    //     });
    //     return $eventUser->minigames()->createMany($minigames->toArray());
    // }

    public function deductTheCoins($condsToBeDeduct)
    {
        return $this->userRepo->deductTheCoins($condsToBeDeduct);
    }

    public function findMGById($miniGameId)
    {
        return EventsMinigame::where(['_id'=> $miniGameId])->firstOrFail();
    }

    public function addMGCompletion($eventUserMG, $miniGameUniqueId, DayWiseMGOutputInterface $dayWiseMGOutputInterface)
    {
        $eventUserMG->where(['mini_games._id'=> $miniGameUniqueId])->push('mini_games.$.completions', ['completed_at'=> new UTCDateTime()]);
        return $dayWiseMGOutputInterface->output($eventUserMG, $miniGameUniqueId);
    }

    public function markMGAsComplete($miniGameData)
    {

        // get day wise minigame record
        $eventUserMG = $this->findMGById($miniGameData->event_minigame_id);

        // add the completion entry to that perticular minigame
        $addedCompletion = $this->addMGCompletion($eventUserMG, $miniGameData->minigame_unique_id, new DayWiseMGOutput);

        return $addedCompletion;
    }

    public function getTodayMiniGame($eventMiniGameId)
    {
        return EventsMinigame::where('from', '>=', new UTCDateTime(today()))->where('to', '<=', new UTCDateTime(today()->endOfDay()))->first();
    }
}