<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\BeforeRegister;

class EmailLogin
{
	use BeforeRegister;

	public function login($request)
	{

		$this->prepare($request);
		$this->user['username'] = strtolower($request->username);
		$this->user['password'] = $request->password;
		return [
    		'user'=> $this->userRepository->createIfNotExist($this->user, ['username'=> $this->user['username'], 'email'=> $this->email], 'email'),
    		'credentials'=> ['email'=> $this->email, 'password'=> $request->password],
			'new_registration'=> $this->userRepository->getCreated()
    	];
	}
}