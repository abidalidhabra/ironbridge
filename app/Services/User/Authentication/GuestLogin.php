<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use stdClass;

class GuestLogin
{
    public function login($request)
    {
    	$name = strtolower(uniqid('ib1779'));
        $user['guest_id'] = $name;
    	$user['address'] = new stdClass();
        $user['password'] = 'ib20171779';
		$user['last_login_as'] = 'guest';
    	$user['location'] = [
    		'type' => 'Point',
    		'coordinates' => [(float)$request->longitude, (float)$request->latitude]
    	];
    	$user['reffered_by'] = $request->reffered_by;
    	$user['firebase_ids'] = [
    		'android_id' => ($request->device_type == 'android')? $request->firebase_id: null,
    		'ios_id'     => ($request->device_type == 'ios')? $request->firebase_id: null,
    	];
    	// $user['additional'] = [ 
    	// 	'device_type'=> $request->device_type,
    	// 	'device_id'=> $request->device_id,
    	// ];
        $user['device_info'] = [ 
            'id'=> $request->device_id,
            'type'=> $request->device_type,
            'model'=> $request->device_model,
            'os'=> $request->device_os
        ];
    	return [
    		'user'=> (new UserRepository)->createIfNotExist($user, ['device_id'=> $request->device_id]),
    		'credentials'=> ['additional.device_id'=> $request->device_id, 'password'=> 'ib20171779']
    	];
    }
}
