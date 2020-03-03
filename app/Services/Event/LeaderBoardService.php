<?php

namespace App\Services\Event;

use App\Models\v1\User;
use App\Models\v3\City;
use App\Models\v3\Event;
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

	public function __construct()
	{
		$this->event = Event::running()
		->whereHas('participations', function($query){
			$query->where('user_id', auth()->user()->id);
		})
		->select('id', 'name')
		->first();
	}

	public function toppers()
	{
		if ($this->event) {
			$this->toppers = (new UserRepository)->getModel()->whereHas('events', function($query){
				$query->where('event_id', $this->event->id);
			})
			->select('_id', 'first_name', 'last_name', 'compasses', 'widgets')
			->orderBy('compasses.remaining', 1)
			->limit(3)
			->get();
		}
		return $this->toppers;
	}

	public function me()
	{
		$userId = new ObjectId(auth()->user()->id);
		$userRank = (new UserRepository)->getModel()->raw(function($collection) use ($userId){
                return $collection->aggregate([
                    [
                        '$addFields'=> [
                            'str_usr_id'=> [ '$toString'=> '$_id' ]
                        ]
                    ],
                    [
                        '$match'=> [
                            '_id'=> $userId
                        ]
                    ],
                    [
                        '$lookup' => [
                            'from' => 'event_users',
                            'let'=> [ 'str_usr_id'=> '$str_usr_id'],
                            'pipeline'=> [
                                [
                                    '$match'=> [ 
                                        '$expr'=> [ 
                                            '$and'=> [
                                               [ '$eq'=> [ '$user_id',  '$$str_usr_id' ] ],
                                               [ '$eq'=> [ '$event_id',  $this->event->id ] ],
                                            ]
                                        ]
                                    ]
                                ],
                               [
                                '$limit'=> 1
                               ]
                            ],
                            'as' => 'event_users'
                        ]
                    ],
                    [
                        '$unwind' => '$event_users'
                    ],
                    [
                        '$sort'=> ['compasses.remaining'=> 1]
                    ],
                    [
                        '$project'=> [
                            '_id'=> true,
                            'name'=> true,
                            'compasses'=> true,
                            'widgets'=> true,
                        ]
                    ],
                    [
                        '$group' => [
                            '_id'   => null,
                            'items' => [
                                '$push'  => '$$ROOT'
                            ]
                        ]
                    ],
                    [
                        '$unwind' => [
                            'path'=> '$items',
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
            return $this->me = $me;
        }
	}

	public function home()
	{
		// $this->response['toppers'] = $this->toppers();
		// if ($me = $this->me()) {
			// $this->response['me'] = $me = $this->me();
		// }
		// $this->response['before'] = $this->more('up', $me->rank);
		// $this->response['after'] = $this->more('down', $me->rank);
		$this->response['before'] = $this->more('up', 50);
		$this->response['after'] = $this->more('down', 50);
		return $this->response();
	}

	public function more($direction, $cursor)
	{

    for ($i=0; $i < 50; $i++) { 
      $digits[] = $i+1;
    }
    // $data = collect($digits)->slice($skip)->take($limit)->values();
  //   $data = collect($digits);
		// $total = $data->count();
		// $data = new Paginator($data, $total, 25, [
  //           'path'  => 'bhula',
  //           'query'  => 'query bhula'
  //       ]);
  //   dd($data);
    $paginate = 25;
		if ($direction == 'up') {
			// if ($cursor <= $paginate) {
			// 	$nextCursor = 0;
			// 	$skip = 3;
			// 	$limit = ($cursor - 1);
			// }else{
			// 	$toBeSkip = ($cursor - 1 - $paginate);
			// 	$skip = $toBeSkip;
			// 	$limit = $paginate;
			// }
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
    $data = collect($digits)->slice($skip)->take($limit)->values();
		// $data = (new UserRepository)->getModel()->whereHas('events', function($query){
		// 	$query->where('event_id', $this->event->id);
		// })
		// ->select('_id', 'first_name', 'last_name', 'compasses', 'widgets')
		// ->orderBy('compasses.remaining', 1)
		// ->skip($skip)
		// ->limit($limit)
		// ->get();

		if ($direction == 'down' && ($data->count() == 0 || $data->count() < $paginate)) {
			$nextCursor = 0;
		}
		return [$direction=> $data, $direction.'_cursor'=> $nextCursor];
		// $userRank = (new UserRepository)->getModel()->raw(function($collection) use ($skip, $limit){
  //               return $collection->aggregate([
  //                   [
  //                       '$addFields'=> [
  //                           'str_usr_id'=> [ '$toString'=> '$_id' ]
  //                       ]
  //                   ],
  //                   [
  //                       '$lookup' => [
  //                           'from' => 'event_users',
  //                           'let'=> [ 'str_usr_id'=> '$str_usr_id'],
  //                           'pipeline'=> [
  //                               [
  //                                   '$match'=> [ 
  //                                       '$expr'=> [ 
  //                                           '$and'=> [
  //                                              [ '$eq'=> [ '$user_id',  '$$str_usr_id' ] ],
  //                                              [ '$eq'=> [ '$event_id',  $this->event->id ] ],
  //                                           ]
  //                                       ]
  //                                   ]
  //                               ],
  //                              [
  //                               '$limit'=> 1
  //                              ]
  //                           ],
  //                           'as' => 'event_users'
  //                       ]
  //                   ],
  //                   [
  //                       '$unwind' => '$event_users'
  //                   ],
  //                   [
  //                       '$sort'=> ['compasses.remaining'=> 1]
  //                   ],
  //                   [
  //                       '$project'=> [
  //                           '_id'=> true,
  //                           'name'=> true,
  //                           'compasses'=> true,
  //                           'widgets'=> true,
  //                       ]
  //                   ],
  //                   [
  //                       '$group' => [
  //                           '_id'   => null,
  //                           'items' => [
  //                               '$push'  => '$$ROOT'
  //                           ]
  //                       ]
  //                   ],
  //                   [
  //                       '$unwind' => [
  //                           'path'=> '$items',
  //                           'includeArrayIndex'=> 'rank',
  //                       ]
  //                   ],
  //               ]);
  //           });

		return $userRank;
	}

	public function up($cursor, $pagination)
	{
		$nextCursor = $cursor - $pagination;

		// if data is not enough to pagination, then return whole data
		if ($cursor <= $pagination) {
			$nextCursor = 0;
			$skip = 3;
			$limit = ($cursor - 1);
		}else{
			// if data is more than pagination, then return whole data
			$toBeSkip = ($cursor - 1 - $pagination);
			$skip = $toBeSkip;
			$limit = $pagination;
		}
	}

	public function after($value='')
	{
		# code...
	}

	public function response()
	{
		return $this->response;
	}
}