<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;

class GoogleLogin
{
    public function login($request)
    {
    	$user['name'] = $request->name;
    	$user['email'] = $request->email;
    	$user['google_id'] = $request->google_id;
        return (new UserRepository)->createIfNotExist($user, ['email'=> $request->email], 'google_id');
    }
}
