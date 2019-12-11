<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use stdClass;

class GoogleLogin
{
	public function login($request)
	{
		$userName = explode(' ', $request->name);
		$user['first_name'] = $userName[0];
		$user['last_name'] = $userName[1] ?? "";
		$user['email'] = strtolower($request->email);
		$user['password'] = 'ib20171779';
		$user['last_login_as'] = 'google';
		$user['google_id'] = $request->google_id;
		$user['address'] = new stdClass();
		$user['location'] = [
			'type' => 'Point',
			'coordinates' => [(float)$request->longitude, (float)$request->latitude]
		];
		$user['device_type'] = $request->device_type;
		$user['reffered_by'] = $request->reffered_by;
		$user['firebase_ids'] = [
			'android_id' => ($request->device_type == 'android')?$request->firebase_id: null,
			'ios_id'     => ($request->device_type == 'ios')?$request->firebase_id: null,
		];
		// $user['additional'] = [ 
		// 	'device_type'=> $request->device_type,
		// 	'device_id'=> $request->device_id
		// ];
		$user['device_info'] = [ 
			'id'=> $request->device_id,
			'type'=> $request->device_type,
			'model'=> $request->device_model,
			'os'=> $request->device_os
		];
		return [
			'user'=> (new UserRepository)->createIfNotExist($user, ['email'=> $user['email']], 'google_id'),
			'credentials'=> ['email'=> $user['email'], 'password'=> 'ib20171779']
		];
	}
}
