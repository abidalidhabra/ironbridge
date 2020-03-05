<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Models\v3\Event;
use App\Repositories\User\UserRepository;
use App\Services\Event\EventUserService;
use App\Services\Event\LeaderBoardService;
use App\Services\User\CompassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;
use Exception;

class EventController extends Controller
{
    
    public function getLeadersBoard(Request $request)
    {
        $data = (new LeaderBoardService)->home();
        return ResponseHelpers::successResponse($data);
    }


    public function getMoreLeaderRanks($skip, $take = 25)
    {
        $data = (new UserRepository)->getModel()->whereHas('events', function($query) use ($eventId){
                    $query->where('event_id', $eventId);
                })
                ->select('_id', 'first_name', 'last_name', 'compasses', 'widgets')
                ->orderBy('compasses.remaining', 1)
                ->skip($skip)
                ->limit($take)
                ->get()
                ->map(function($user){
                    $user->avatar = asset('storage/avatars/'.$user->id.'.jpg');
                    return $user;
                });
        return $data;
    }

    public function getMoreLeaders(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'cursor'=> 'required|integer',
            'direction'  => 'required|in:up,down',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()]);
        }
        $data = (new LeaderBoardService)->next($request->direction, $request->cursor);
        return ResponseHelpers::successResponse($data);
    }

    public function reduceTheRadius(Request $request)
    {
        try {
            $event = (new EventUserService)->setUser(auth()->user())->running(['*'], true);
            $eventUser = (new CompassService)
                            ->setUser(auth()->user())
                            ->setEvent($event)
                            ->setEventUser($event->participations->first())
                            ->deduct();
            return ResponseHelpers::successResponse($eventUser->response());
        } catch (Exception $e) {
            return ResponseHelpers::validationErrorResponse($e);
        }
    }
}
