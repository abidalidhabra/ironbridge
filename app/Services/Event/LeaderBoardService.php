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
use Exception;

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
    public $myRank;
    public $beforeCursor;
    public $afterCursor;

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

        if (!$this->event) {
            throw new Exception("Seems like you are not participated in any of event yet.");
        }
	}

	public function home()
	{
        $this->toppers();
        $this->me();
		$this->more('up', $this->myRank);
		$this->more('down', $this->myRank);
        $this->getUsers();
        $this->map();
		return $this->response();
	}

  public function next($direction, int $cursor)
  {
    $this->more($direction, $cursor);
    $this->getUsers();
    $this->map($cursor);
    return $this->response();
  }

    public function toppers()
    {
        $this->toppers = EventUser::raw(function($collection) {
                return $collection->aggregate([
                    [
                        '$sort'=> ['compasses.remaining'=> -1]
                    ],
                    [
                        '$limit'=> 3
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

        $this->toppers->map(function($user){
            $user->rank += 1;
            return $user;
        });

        $this->userIds = array_merge($this->userIds, $this->toppers->pluck('participations')->pluck('user_id')->toArray(), [auth()->user()->id]);

        return $this->toppers;
    }

    public function me()
    {
        $userRank = EventUser::raw(function($collection) {
                return $collection->aggregate([
                    [
                        '$sort'=> ['compasses.remaining'=> -1]
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
                    ],
                    [
                        '$match'=> [
                            'participations.user_id'=> auth()->user()->id,
                            'participations.event_id'=> $this->event->id,
                        ]
                    ]
                ]);
            });

        if ($me = $userRank->first()) {
            $me->rank += 1;
            // if ($me->rank > 3) {
            //     unset($me->items->widgets);
            // }
            $this->myRank = $me->rank;
            return $this->me = collect([$me]);
        }
    }

    public function more($direction, $cursor)
    {

        $paginate = 3;
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

        if ($limit) {
            $data = EventUser::raw(function($collection) use ($skip, $limit) {
                    return $collection->aggregate([
                        [
                            '$sort'=> ['compasses.remaining'=> -1]
                        ],
                        [
                            '$skip'=> $skip
                        ],
                        [
                            '$limit'=> $limit
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
            $data->map(function($user){
                $user->rank += 1;
            });
        }else{
            $data = collect();
        }


        $this->userIds = array_merge($this->userIds, $data->pluck('participations')->pluck('user_id')->toArray());

        if ($direction == 'down' && ($data->count() == 0 || $data->count() < $paginate)) {
            $nextCursor = 0;
        }

        if ($direction == 'up') {
            $this->beforeCursor = ($nextCursor)? $nextCursor: 0;
            $this->before = $data;
        }else{
            $this->afterCursor = ($nextCursor)? $nextCursor: 0;
            $this->after = $data;
        }
        return [$direction.'_data'=> $data, 'cursor'=> ($nextCursor)? $nextCursor+1: 0];
    }

    public function getUsers()
    {
        $this->users = (new UserRepository)->getModel()->whereIn('_id', $this->userIds)
                ->select('_id', 'first_name', 'last_name', 'widgets', 'gender')
                ->get()
                ->map(function($user){
                  $user->avatar = asset('storage/avatars/'.$user->id.'.jpg');
                  return $user;
                })
                ->toArray();
        return $this;
    }

    public function map($cursor = null)
    {
        $users = collect($this->users);
        

        if($this->toppers){
            $this->response['toppers'] = $this->toppers->map(function($user) use ($users){
                $temp = $users->where('_id', $user->participations->user_id)->values()->first();
                $user->user_id = $temp['_id'];
                $user->first_name = $temp['first_name'];
                $user->last_name = $temp['last_name'];
                $user->avatar = $temp['avatar'];
                $user->widgets = collect($temp['widgets'])->where('selected', true)->values();
                $user->gender = $temp['gender'];
                $user->compasses = $user->participations->compasses;
                unset($user->participations, $user->_id);
                return $user;
            });
        }
        
        if($this->me){
            $this->response['me'] = $this->me->map(function($user) use ($users){
                $temp = $users->where('_id', $user->participations->user_id)->values()->first();
                $user->user_id = $temp['_id'];
                $user->first_name = $temp['first_name'];
                $user->last_name = $temp['last_name'];
                $user->avatar = $temp['avatar'];
                $user->compasses = $user->participations->compasses;
                if ($user->rank <= 3) {
                    $user->widgets = collect($temp['widgets'])->where('selected', true)->values();
                }
                unset($user->participations, $user->_id);
                return $user;
            });
        }
        
        if($this->before){
            $this->response['before'] = $this->before->map(function($user) use ($users, $cursor){
                $temp = $users->where('_id', $user->participations->user_id)->values()->first();
                $user->user_id = $temp['_id'];
                $user->first_name = $temp['first_name'];
                $user->last_name = $temp['last_name'];
                $user->avatar = $temp['avatar'];
                $user->rank = ($cursor)? $cursor + $user->rank: $this->myRank - $user->rank;
                $user->compasses = $user->participations->compasses;
                unset($user->participations, $user->_id);
                return $user;
            });
            $this->response['before_cursor'] = $this->beforeCursor;
        }
        
        if($this->after){
            $this->response['after'] = $this->after->map(function($user) use ($users, $cursor){
                $temp = $users->where('_id', $user->participations->user_id)->values()->first();
                $user->user_id = $temp['_id'];
                $user->first_name = $temp['first_name'];
                $user->last_name = $temp['last_name'];
                $user->avatar = $temp['avatar'];
                $user->compasses = $user->participations->compasses;
                $user->rank = ($cursor)? $cursor + $user->rank: $this->myRank + $user->rank;
                unset($user->participations, $user->_id);
                return $user;
            });
            $this->response['after_cursor'] = $this->afterCursor;
        }
    }

	public function response()
	{
		return $this->response;
	}
}