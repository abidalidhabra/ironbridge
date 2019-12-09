<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;

class GuestLogin
{
    public function login($request)
    {
    	$user['name'] = $request->name;
    	$user['device_id'] = $request->device_id;
        return (new UserRepository)->createIfNotExist($user, ['device_id'=> $request->device_id]);
    }
}
