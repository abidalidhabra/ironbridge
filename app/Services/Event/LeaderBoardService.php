<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
use App\Models\v3\EventUser;
use App\Repositories\User\UserRepository;
use App\Services\Traits\UserTraits;
use DateTime;
use DateTimeZone;
use Illuminate\Pagination\Paginator;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class LeaderBoardService
{
	use UserTraits;

	public $event;
	public $toppers;
	public $me;
	public $before;
	public $after;
	public $response;
    public $userIds;
    public $users;

	public function __construct()
	{
        $this->users = [];
        $this->userIds = [];
		$this->event = Event::running()
                		->whereHas('participations', function($query){
                			$query->where('user_id', auth()->user()->id);
                		})
                		->select('id', 'name')
                		->first();
	}

	public function home()
	{
        $this->toppers();
        $this->me();
		$this->more('up', 52);
		$this->more('down', 52);
        $this->getUsers();
        $this->map();
		return $this->response();
	}

  public function next($direction, $cursor)
  {

    $this->response = $this->more($direction, $cursor);
    return $this->response();
  }

    public function toppers()
    {
        if ($this->event) {
            $this->toppers = EventUser::where('event_id', $this->event->id)
            ->select('_id', 'user_id', 'event_id', 'compasses')
            ->orderBy('compasses.remaining', 1)
            ->limit(3)
            ->get();
        }
        $this->userIds = array_merge($this->userIds, $this->toppers->pluck('user_id')->toArray(), [auth()->user()->id]);
        return $this->toppers;
    }

    public function me()
    {
        $userRank = EventUser::raw(function($collection) {
                return $collection->aggregate([
                    [
                        '$match'=> [
                            'user_id'=> auth()->user()->id,
                            'event_id'=> $this->event->id,
                        ]
                    ],
                    [
                        '$sort'=> ['compasses.remaining'=> 1]
                    ],
                    [
                        '$project'=> [
                            '_id'=> true,
                            'user_id'=> true,
                            'event_id'=> true,
                            'compasses'=> true,
                        ]
                    ],
                    [
                        '$group' => [
                            '_id'   => null,
                            'participations' => [
                                '$push'  => '$$ROOT'
                            ]
                        ]
                    ],
                    [
                        '$unwind' => [
                            'path'=> '$participations',
                            'includeArrayIndex'=> 'rank',
                        ]
                    ]
                ]);
            });

        if ($me = $userRank->first()) {
            $me->rank += 1;
            if ($me->rank > 3) {
                unset($me->items->widgets);
            }
            return $this->me = collect([$me]);
        }
    }

	public function more($direction, $cursor)
	{

        // for ($i=0; $i < 150; $i++) { 
        //     $digits[] = $i+1;
        // }
        $paginate = 25;
        if ($direction == 'up') {
            $limit = (($cursor - 1) == 0)? 0: ($cursor - 1);
            if ($limit > $paginate) {
                $limit = $paginate;
            }
            $skip = (($cursor - 1 - $paginate) < 0)? 0: ($cursor - 1 - $paginate);
            $nextCursor = $skip;
        }else if ($direction == 'down'){
            $nextCursor = $cursor + $paginate;
            $skip = $cursor;
            $limit = $paginate;
        }

		// dd($skip, $limit, $cursor, $paginate);
        // $data = collect($digits)->slice($skip)->take($limit)->values();
		$data = EventUser::where('event_id', $this->event->id)
        		->select('_id', 'event_id', 'user_id', 'compasses')
        		->orderBy('compasses.remaining', 1)
        		->skip($skip)
        		->limit($limit)
        		->get();
        $this->userIds = array_merge($this->userIds, $data->pluck('user_id')->toArray());

		if ($direction == 'down' && ($data->count() == 0 || $data->count() < $paginate)) {
			$nextCursor = 0;
		}
		return [$direction.'_data'=> $data, 'cursor'=> ($nextCursor)? $nextCursor+1: 0];
	}

    public function getUsers()
    {
        $this->users = (new UserRepository)->getModel()->whereIn('_id', $this->userIds)
                ->select('_id', 'first_name', 'last_name', 'widgets')
                ->get()
                ->map(function($user){
                  $user->avatar = asset('storage/avatars/'.$user->id.'.jpg');
                  return $user;
                })
                ->toArray();
        return $this;
    }

    public function map()
    {
        $users = collect($this->users);
        $this->response['toppers'] = $this->toppers->map(function($user) use ($users){
            $user->datas = $users->where('_id', $user->user_id)->values();
            return $user;
        });

        $this->response['me'] = $this->me->map(function($user) use ($users){
            $user->datas = $users->where('_id', $user->participations->user_id)->values();
            return $user;
        });

        $this->response['before'] = $this->me->map(function($user) use ($users){
            $user->datas = $users->where('_id', $user->user_id)->values();
            return $user;
        });

        $this->response['after'] = $this->me->map(function($user) use ($users){
            $user->datas = $users->where('_id', $user->user_id)->values();
            return $user;
        });
    }

	public function response()
	{
		return $this->response;
	}
}