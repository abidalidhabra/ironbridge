<?php

namespace App\Helpers;
use App\Models\v1\Avatar;
use App\Models\v1\City;
use App\Models\v1\WidgetItem;
use App\Repositories\EventRepository;
use Auth;
use MongoDB\BSON\ObjectId as MongoID;
use Request;
use Route;

class UserHelper {
	
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
		$eventsCities = City::select('_id','name')->havingActiveEvents()->get()
		return [
			'avatars' => $avatars,
			'widgets' => $widgets,
			'user_avatar' => $user->avatar,
			'user_widgets' => $user->widgets,
			'events_cities' => $eventsCities,
			// 'used_widgets' => $user->used_widgets,
			// 'plans' => $plans,
			// 'events_data' => $events,
			// 'cities' => $cities,
		];
	}

	public static function getWidgets($user)
	{
		$widgets = WidgetItem::select('_id','widget_name','item_name','gold_price', 'avatar_id')->get();
		$widgets = $widgets->groupBy('widget_name');
		return $widgets;
	}

	public static function getAvatars($user)
	{
		$avatars = Avatar::select('_id','name','gender','eyes_colors','hairs_colors','skin_colors')->get();
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
}