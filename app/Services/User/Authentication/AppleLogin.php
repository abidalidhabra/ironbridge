<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\BeforeRegister;

class AppleLogin
{
	use BeforeRegister;

	public function login($request)
	{

		$this->prepare($request);
		$this->user['apple_id'] = $request->apple_id;
		$this->user['apple_data'] = $request->apple_data;
		return [
    		'user'=> $this->userRepository->createIfNotExist($this->user, ['apple_id'=> $this->user['apple_id']], 'apple_id'),
    		'credentials'=> ['apple_id'=> $this->user['apple_id'], 'password'=> $this->password],
			'new_registration'=> $this->userRepository->getCreated()
    	];
	}
}