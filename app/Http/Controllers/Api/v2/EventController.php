<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Models\v3\Event;
use App\Repositories\User\UserRepository;
use App\Services\Event\LeaderBoardService;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class EventController extends Controller
{
    
    public function getLeadersBoard(Request $request)
    {
    	// $event = $this->getRunningEvent();
    	// if ($event) {
     //        $eventId = $event->id;
    	// 	$response['toppers'] = $this->topUsersRank($eventId);
     //        $id = new ObjectId("5e3bb937ab47000056004052");
     //        if ($me = $this->getUserRank($id, $eventId)) {
     //            $response['me'] = $me;
     //            $response['before'] = $this->getMoreLeaderRanks();
     //            $response['after'] = $this->getMoreLeaderRanks();
     //        }
     //    }

        $data = (new LeaderBoardService)->home();
        return ResponseHelpers::successResponse($data ?? []);
    }

    // public function getRunningEvent()
    // {
    //     $event = Event::running()
    //             ->whereHas('participations', function($query){
    //                 $query->where('user_id', auth()->user()->id);
    //             })
    //             ->select('id', 'name')
    //             ->first();
    //     return $event;
    // }

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

    // public function getUserRank($userId, $eventId)
    // {
    //     $userRank = (new UserRepository)->getModel()->raw(function($collection) use ($userId, $eventId){
    //             return $collection->aggregate([
    //                 [
    //                     '$addFields'=> [
    //                         'str_usr_id'=> [ '$toString'=> '$_id' ]
    //                     ]
    //                 ],
    //                 [
    //                     '$match'=> [
    //                         '_id'=> $userId
    //                     ]
    //                 ],
    //                 [
    //                     '$lookup' => [
    //                         'from' => 'event_users',
    //                         'let'=> [ 'str_usr_id'=> '$str_usr_id'],
    //                         'pipeline'=> [
    //                             [
    //                                 '$match'=> [ 
    //                                     '$expr'=> [ 
    //                                         '$and'=> [
    //                                            [ '$eq'=> [ '$user_id',  '$$str_usr_id' ] ],
    //                                            [ '$eq'=> [ '$event_id',  $eventId ] ],
    //                                         ]
    //                                     ]
    //                                 ]
    //                             ],
    //                            [
    //                             '$limit'=> 1
    //                            ]
    //                         ],
    //                         'as' => 'event_users'
    //                     ]
    //                 ],
    //                 [
    //                     '$unwind' => '$event_users'
    //                 ],
    //                 [
    //                     '$sort'=> ['compasses.remaining'=> 1]
    //                 ],
    //                 [
    //                     '$project'=> [
    //                         '_id'=> true,
    //                         'name'=> true,
    //                         'compasses'=> true,
    //                         'widgets'=> true,
    //                     ]
    //                 ],
    //                 [
    //                     '$group' => [
    //                         '_id'   => null,
    //                         'items' => [
    //                             '$push'  => '$$ROOT'
    //                         ]
    //                     ]
    //                 ],
    //                 [
    //                     '$unwind' => [
    //                         'path'=> '$items',
    //                         'includeArrayIndex'=> 'rank',
    //                     ]
    //                 ]
    //             ]);
    //         });

    //     if ($me = $userRank->first()) {
    //         $me->rank += 1;
    //         if ($me->rank > 3) {
    //             unset($me->items->widgets);
    //         }
    //         return $me;
    //     }else{
    //         return null;
    //     }
    // }

    // public function topUsersRank($eventId)
    // {
    //     $data = (new UserRepository)->getModel()->whereHas('events', function($query) use ($eventId){
    //         $query->where('event_id', $eventId);
    //     })
    //     ->select('_id', 'first_name', 'last_name', 'compasses', 'widgets')
    //     ->orderBy('compasses.remaining', 1)
    //     ->limit(3)
    //     ->get();
    //     return $data;
    // }
}
