<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;

class AppleLogin
{
	public function login($request)
	{
		$user['name'] = $request->name;
		$user['email'] = $request->email;
		$user['apple_id'] = $request->apple_id;
        return (new UserRepository)->createIfNotExist($user, ['email'=> $request->email], 'apple_id');
	}
}