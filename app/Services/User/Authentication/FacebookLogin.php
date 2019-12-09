<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;

class FacebookLogin
{
	public function login($request)
	{
		$user['name'] = $request->name;
		$user['email'] = $request->email;
		$user['facebook_id'] = $request->facebook_id;
        return (new UserRepository)->createIfNotExist($user, ['email'=> $request->email], 'facebook_id');
	}
}