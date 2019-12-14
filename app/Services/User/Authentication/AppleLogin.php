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
		return [
    		'user'=> $this->userRepository->createIfNotExist($this->user, ['email'=> $this->email], 'apple_id'),
    		'credentials'=> ['email'=> $this->email, 'password'=> $this->password],
			'new_registration'=> $this->userRepository->getCreated()
    	];
	}
}