<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\BeforeRegister;

class GuestLogin
{
    use BeforeRegister;

    public function login($request)
    {

        $this->prepareForGuest($request);
    	return [
    		'user'=> $this->userRepository->createIfNotExist($this->user, ['device_info.id'=> $request->device_id], 'guest_id'),
    		'credentials'=> ['device_info.id'=> $request->device_id, 'password'=> $this->password],
            'new_registration'=> $this->userRepository->getCreated()
    	];
    }
}
