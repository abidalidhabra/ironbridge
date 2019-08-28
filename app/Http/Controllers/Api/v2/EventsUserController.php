<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\ParticipateInEventRequest;
use App\Http\Requests\v2\PresentDayEventDetailRequest;
use App\Refacing\Contracts\EventRefaceInterface;
use App\Refacing\Contracts\EventsMiniGameRefaceInterface;
use App\Repositories\Contracts\EventInterface;
use App\Repositories\Contracts\EventsMiniGameInterface;
use App\Repositories\Contracts\EventsUserInterface;
use App\Repositories\Contracts\UserInterface;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class EventsUserController extends Controller
{

    private $user;
    private $userInterface;
    private $eventInterface;
    private $eventsUserInterface;
    private $eventsMiniGameInterface;
    private $eventRefaceInterface;

    public function __construct(
        EventInterface $eventInterface, 
        EventsUserInterface $eventsUserInterface, 
        EventsMiniGameInterface $eventsMiniGameInterface,
        EventsMiniGameRefaceInterface $eventsMiniGameRefaceInterface,
        EventRefaceInterface $eventRefaceInterface
    )
    {
        $this->eventInterface = $eventInterface;
        $this->eventsUserInterface = $eventsUserInterface;
        $this->eventsMiniGameInterface = $eventsMiniGameInterface;
        $this->eventsMiniGameRefaceInterface = $eventsMiniGameRefaceInterface;
        $this->eventRefaceInterface = $eventRefaceInterface;
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $this->user = $user;
            $this->userInterface = app(UserInterface::class)($user);
            return $next($request);
        });
    }

    public function participateInEvent(ParticipateInEventRequest $request)
    {
        try {

            $event = $this->eventInterface->find($request->event_id, ['_id', 'name', 'discount_countdown', 'discount_till', 'play_countdown', 'starts_at', 'status', 'event_days', 'fees']);

            // shot the event user data into the database
            $eventsUser = $this->eventsUserInterface->createByUser($this->user, $event);
            // $eventsUser->delete();
            // prepare the mingame's data prior to insert
            $miniGamesData = $this->eventsMiniGameRefaceInterface->prepareToInsert($event->event_days);

            // shot minigame data into the database
            $eventsUserMiniGames = $this->eventsMiniGameInterface->createByEventsUser($eventsUser, $miniGamesData);
            // $eventsUserMiniGames->each(function($data){ $data->delete(); });

            // deduct the coins from user's account
            $availableGold = $this->userInterface->deductGold($event->fees);

            // prepare output of minigames for the client
            $eventsUserMiniGamesReface = $this->eventsMiniGameRefaceInterface->output($event, $eventsUserMiniGames);
            
            // prepare output of event for the client
            $eventReface = $this->eventRefaceInterface->output($event);
            
            return ResponseHelpers::successResponse([
                'event'=> $eventReface,
                'event_user'=> $eventsUser, 
                'event_user_minigames'=> $eventsUserMiniGamesReface, 
                'available_gold'=> $availableGold
            ]);
        } catch (Throwable $e) {
            return ResponseHelpers::errorResponse($e);
        } catch (Exception $e) {
            return ResponseHelpers::errorResponse($e);
        }
    }

    public function getPresentDayEventDetail(PresentDayEventDetailRequest $request)
    {

        $eventsUser = $this->eventsUserInterface->find($request->events_user_id);

        // get the corresponding event data from eventUser
        $event = $this->eventsUserInterface->event($eventsUser, ['_id', 'name', 'discount_till', 'discount_countdown', 'starts_at', 'play_countdown', 'status']);
        
        // get the corresponding minigames data from eventUser
        $miniGames = $this->eventsUserInterface->miniGames($eventsUser);

        // prepare output for the client

        $eventsUserMiniGames = $this->eventsMiniGameRefaceInterface->output($event, $miniGames);

        return ResponseHelpers::successResponse([
            'event'=> $event,
            'event_user'=> $eventsUser,
            'event_user_minigames'=> $eventsUserMiniGames
        ]);
    }
}
