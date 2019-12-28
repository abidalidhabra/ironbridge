<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use stdClass;

trait BeforeRegister
{
	
	protected $user;
	protected $email;
	protected $password;
	protected $type;
	protected $userRepository;
	
	public function __construct()
	{
		$this->userRepository = new UserRepository;
	}

	public function prepare($request)
	{

		$this->email = strtolower($request->email);
		$this->password = 'ib20171779';
		$this->type = $request->type;

		if ($request->filled('name')) {
			$userName = explode(' ', $request->name);
			$this->user['first_name'] = $userName[0];
			$this->user['last_name'] = $userName[1] ?? "";
			$this->user['email'] = $this->email;
		}
		$this->user['password'] = 'ib20171779';
		$this->user['last_login_as'] = $this->type;
		$this->user['address'] = new stdClass();
		$this->user['location'] = [
			'type' => 'Point',
			'coordinates' => [(float)$request->longitude, (float)$request->latitude]
		];
		$this->user['device_type'] = $request->device_type;
		$this->user['reffered_by'] = $request->reffered_by;
		$this->user['firebase_ids'] = [
			'android_id' => ($request->device_type == 'android')?$request->firebase_id: null,
			'ios_id'     => ($request->device_type == 'ios')?$request->firebase_id: null,
		];

		$this->user['device_info'] = [ 
			'id'=> $request->device_id,
			'type'=> $request->device_type,
			'model'=> $request->device_model,
			'os'=> $request->device_os
		];

		$this->user['skeleton_keys'] = [
			[ 
				'key' => new ObjectId(),
				'created_at' => new UTCDateTime(),
				'used_at' => null
			]
		];
	}

	public function prepareForGuest($request)
	{

		$this->password = 'ib20171779';
		$this->type = $request->type;

		$name = strtolower(uniqid('ib1779'));
		$this->user['first_name'] = $name;
        $this->user['guest_id'] = $name;
    	$this->user['address'] = new stdClass();
        $this->user['password'] = 'ib20171779';
		$this->user['last_login_as'] = 'guest';
    	$this->user['location'] = [
    		'type' => 'Point',
    		'coordinates' => [(float)$request->longitude, (float)$request->latitude]
    	];
    	$this->user['reffered_by'] = $request->reffered_by;
    	$this->user['firebase_ids'] = [
    		'android_id' => ($request->device_type == 'android')? $request->firebase_id: null,
    		'ios_id'     => ($request->device_type == 'ios')? $request->firebase_id: null,
    	];
        $this->user['device_info'] = [ 
            'id'=> $request->device_id,
            'type'=> $request->device_type,
            'model'=> $request->device_model,
            'os'=> $request->device_os
        ];
        $this->user['skeleton_keys'] = [
            [ 
                'key' => new ObjectId(),
                'created_at' => new UTCDateTime(),
                'used_at' => null
            ]
        ];
	}
}