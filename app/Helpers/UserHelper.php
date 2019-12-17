<?php

namespace App\Helpers;
use App\Models\v1\Avatar;
use App\Models\v1\City;
use App\Models\v1\Game;
use App\Models\v1\News;
use App\Models\v1\WidgetItem;
use App\Models\v2\HuntStatistic;
use App\Repositories\EventRepository;
use App\Repositories\Game\GameRepository;
use App\Repositories\MinigameHistoryRepository;
use App\Repositories\RelicRepository;
use App\Repositories\User\UserRepository;
use Auth;
use MongoDB\BSON\ObjectId as MongoID;
use MongoDB\BSON\UTCDateTime;
use Request;
use Route;

class UserHelper {
	
	public $user;

	public static function getPerfixDetails($user = "")
	{

		if (!$user) {
			$user = Auth::user();
		}

		$avatars = self::getAvatars($user);
		$widgets = self::getWidgets($user);
		
		// $request 	= Request::create('/api/v1/getThePlans', 'GET');
		// $response 	= Route::dispatch($request);
		// $plans 		= $response->getData()->data;

		// $request = new Request();
		// $request = Request::create('/api/v1/getTheEvents', 'GET', ['page' => 1]);
		// Request::replace($request->input());
		// $response 	= Route::dispatch($request);
		// $events 	= $response->getData()->data;

        // $cities = City::select('_id','name')->get();

		// $eventsCities = (new EventRepository($user))->cities();
		$eventsCities = City::select('_id','name')->havingActiveEvents()->get();
		// $relics = (new RelicRepository)->getModel()
		// 			->active()
		// 			// ->whereHas('season', function($query) {
		// 			// 	$query->active();
		// 			// })
		// 			->with(['participations'=> function($query) use ($user) {
		// 				$query->where('user_id', $user->id)->select('id', 'hunt_id', 'user_id');
		// 			}])
		// 			->whereDoesntHave('participations', function($query) use ($user) {
		// 				$query->where(['user_id'=> $user->id, 'status'=> 'completed']);
		// 			})
		// 			->with('season:_id,name')
		// 			->select('_id', 'name', 'desc', 'active', 'icon', 'complexity', 'season_id', 'fees')
		// 			->get();
		// $relicsInfo = $user->relics_info()->select('_id', 'complexity', 'icon')->get();
		// $relics = $user->relics->map(function($relic) use ($relicsInfo) { 
		// 	// dd($relic, $relicsInfo->toArray());
		// 	$relic['info'] = $relicsInfo->where('_id', $relic['id'])->first(); 
		// 	return $relic; 
		// });
		$userRepository = (new UserRepository($user));
		$huntStatistics = (new HuntStatistic)->first(['id', 'distances', 'refreshable_distances']);
		return [
			'avatars' => $avatars,
			'widgets' => $widgets,
			'user_avatar' => $user->avatar,
			'user_widgets' => $user->widgets,
			'tutorials' => $user->tutorials,
			'events_cities' => $eventsCities,
			'free_outfit_occupied' => $user->free_outfit_taken,
			'latest_news' => News::latest()->limit(1)->get()->map(function($news) { return $news->setHidden(['valid_till', 'updated_at']); }),
			// 'relics' => $relics,
			'streaming_relic' => $userRepository->streamingRelic(),
			// 'available_complexities' => $user->getAvailableComplexities(),
			'available_complexities' => [1],
			'agent_stack'=> $userRepository->getAgentStack(),
			'hunt_statistics'=> array_merge($huntStatistics->toArray(), ['power_station'=> ['till'=> $userRepository->powerFreezeTill()]]),
			// 'used_widgets' => $user->used_widgets,
			// 'plans' => $plans,
			// 'events_data' => $events,
			// 'cities' => $cities,
		];
	}

	public static function getWidgets($user)
	{
		$widgets = WidgetItem::select('_id','widget_name','item_name','gold_price', 'avatar_id', 'free', 'default')->orderBy('gold_price', 'asc')->get();
		$widgets = $widgets->groupBy('widget_name');
		return $widgets;
	}

	public static function getAvatars($user)
	{
		$avatars = Avatar::select('_id','name','gender','eyes_colors','hairs_colors','skin_colors')
					->get()
					->map(function($avatar) {
						$avatar->hairs_colors = array_filter($avatar->hairs_colors,'strlen');
						return $avatar;
					});
		return $avatars;
	}

	public static function getUserLocation($lat,$long)
	{

		$location = app('geocoder')->reverse($lat,$long)->get();
		$formatter = new \Geocoder\Formatter\StringFormatter();

		$locationObj = (array)$location->first();
		if (!empty($locationObj)) {

			$streetNumber = $location->first()->getStreetNumber(); 
			$streetName = $location->first()->getStreetName();

			$cityPrimary = $formatter->format($location->first(), '%L');
			$cityDistrict = $formatter->format($location->first(), '%D');
			$city = ($cityPrimary)?$cityPrimary:$cityDistrict;
			
			$postalCode = $location->first()->getPostalCode(); 
			$countryInfo = $location->first()->getCountry(); 
			$country['name'] = $countryInfo->getName();
			$country['code'] = $countryInfo->getCode();

			$response['street_number'] = ($streetNumber)?$streetNumber:"";
			$response['street_name'] = ($streetName)?$streetName:"";
			$response['city'] = ($city)?$city:"";
			$response['postal_code'] = ($postalCode)?$postalCode:"";
			$response['country'] = $country;

			// $streetNumber = $location->first()->getStreetNumber(); 
			// $streetName = $location->first()->getStreetName(); 
			// $locality = $location->first()->getLocality(); 
			// $postalCode = $location->first()->getPostalCode(); 
			// $subLocality = $location->first()->getSubLocality(); 
			// $countryInfo = $location->first()->getCountry(); 
			// $country['name'] = $countryInfo->getName();
			// $country['code'] = $countryInfo->getCode();
			// $adminLevels = $location->first()->getAdminLevels()->all();
			
			// $response['street_number'] = ($streetNumber)?$streetNumber:"";
			// $response['street_name'] = ($streetName)?$streetName:"";
			// $response['locality'] = ($locality)?$locality:"";
			// $response['postal_code'] = ($postalCode)?$postalCode:"";
			// $response['sub_locality'] = ($subLocality)?$subLocality:"";
			// $response['country'] = $country;
			// foreach($adminLevels as $index => $adminLevel){
			// 	$response['admin_levels'][$index]['level'] = $adminLevel->getLevel();
			// 	$response['admin_levels'][$index]['name'] = $adminLevel->getName();
			// 	$response['admin_levels'][$index]['code'] = $adminLevel->getCode();
			// }
			return $response;
		}else{
			\Log::info('DONE NOW');
			return null;
		}
	}

	// public static function chargeTheUser($charge)
	// {
	// 	try {
	// 		$chargeInfo = \Stripe\Charge::create($charge);
	// 		return ['status'=>true,'message'=>'Payment has been completed successfully','chargeInfo'=>$chargeInfo]; 
	// 	}catch (\Stripe\Error\InvalidRequest $e) {
			
	// 		$body = $e->getJsonBody();
	// 		$err  = $body['error'];
	// 		return ['status'=>false,'message'=>$err]; 
	// 	} catch (\Stripe\Error\Card $e) {

	// 		$body = $e->getJsonBody();
	// 		$err  = $body['error'];

	// 		if ($err['code'] == 'resource_missing') {
	// 			return ['status'=>false,'message'=>'Card Missing!!! Please save the card first'];
	// 		}
	// 		return ['status'=>false,'message'=>'Wrong Card information provided'];
	// 	} catch (\Stripe\Error\Authentication $e) {

	// 		return ['status'=>false,'message'=>'Problem occures with Authentications'];
	// 	} catch (\Stripe\Error\ApiConnection $e) {
			
	// 		return ['status'=>false,'message'=>'Something went wrong with api connection'];
	// 	} catch (\Stripe\Error\Base $e) {

	// 		return ['status'=>false,'message'=>'Base Error occures'];
	// 	} catch(Exception $e){
	// 		return ['status'=>false,'message'=>'Something went wrong'];
	// 	}
	// }

	public static function addRequiredFlags($events)
	{

		
		$events->getCollection()->transform(function($event, $index){

			$actualMaximumLevel = $event->event_levels->pluck('level')->max();
			$usersMaximumLevel  = null;
			$isUserParticipated = $event->event_participations->count();
			if ($isUserParticipated) {
				$usersMaximumLevel = collect($event->event_participations->first()->completed_levels)->max();
			}
			
			/** [UNPARTICIPATED, PARTICIPATED, COMPLETED] **/
			if ($actualMaximumLevel == $usersMaximumLevel) {
            	$event->event_action = 'COMPLETED';
			}else{
            	$event->event_action = ($isUserParticipated)?'PARTICIPATED':'UNPARTICIPATED'; 
			}
			unset($event->event_participations->first()->completed_levels);
            return $event;
        });

        return $events;
	}

	public static function playPractiveEvent($user, $token)
	{

		/** Participate the user into the test event **/
        if (!$user->event_participations()->where('event_id',env('PRACTICE_EVENT_ID'))->count()) {
            // $request = Request::create('/api/v1/addParticipation', 'POST',['event_id'=>env('PRACTICE_EVENT_ID'), 'token'=>$token]);
            $request = Request::create('/api/v1/addParticipation', 'POST',['event_id'=>env('PRACTICE_EVENT_ID')]);
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('Authorization', 'Bearer '.$token);
			Request::replace($request->input());
            $response   = Route::dispatch($request);
            $apiRes     = $response->getData();
            dd($request);
            // dd($apiRes);
            if ($response->status() != 200) {
                return response()->json([ 'message' => 'internal api exception while participating into practice event.'],500);
            }
        }
        return response()->json([ 'message' => 'Participation into the practice event completed successfully.'],200);
	}


	public static function minigameTutorials($user){
		
        if (!$user->minigame_tutorials->count()) {
            $games = Game::where('status',true)->get();
            $minigameTutorial = []; 

            foreach ($games as $key => $game) {
            	if ($game->practice_default_active) {
                	$minigameTutorial[] = [ 'game_id' => $game->id, 'completed_at' => new UTCDateTime(now()) ];
                }else{
                	$minigameTutorial[] = [ 'game_id' => $game->id, 'completed_at' => null ];
                }
            }

            $user->minigame_tutorials = $minigameTutorial;
            $user->save();
        }
    }

    public function setUser($user)
    {
    	$this->user = $user;
    	return $this;
    }

    public function getMinigamesStatistics()
    {
    	$gameStatistics = (new MinigameHistoryRepository)->getModel()->raw(function ($collection) {
	    		return $collection->aggregate(
	    			[
	    				[ 
	    					'$match' => [ 'user_id'=> $this->user->id, 'action'=> 'completed' ]
	    				],
	    				[ 
	    					'$group' => [
	    						'_id' => '$game_id', 
	    						'game_id' => ['$last'=> '$game_id'], 
	    						'completion_times' => [ '$sum'=> 1 ],
	    						'highest_score'=> ['$max'=> '$score']
	    					]
	    				]
	    			]
	    		);
	    	}
	    );

    	$games = (new GameRepository)->getModel()->whereIn('_id', $gameStatistics->pluck('game_id'))->select('_id', 'name', 'identifier')->get();
    	$games->map(function($game, $index) use ($gameStatistics) {
    		$statistics = $gameStatistics->where('game_id', $game->id)->first();
    		$game->completion_times = $statistics->completion_times;
    		$game->highest_score = $statistics->highest_score;
    		return $game;
    	});
    	return $games;
    }
}