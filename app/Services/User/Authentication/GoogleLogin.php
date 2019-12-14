<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\BeforeRegister;

class GoogleLogin
{
	use BeforeRegister;

	public function login($request)
	{

		$this->prepare($request);
		$this->user['google_id'] = $request->google_id;
		return [
			'user'=> $this->userRepository->createIfNotExist($this->user, ['email'=> $this->email], 'google_id'),
    		'credentials'=> ['email'=> $this->email, 'password'=> $this->password],
			'new_registration'=> $this->userRepository->getCreated()
		];
	}
}
