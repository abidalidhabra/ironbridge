<?php

namespace App\Services\User\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\BeforeRegister;
use Exception;

class GuestLogin
{
    use BeforeRegister;

    public function login($request)
    {

        $this->prepareForGuest($request);
        $user = $this->userRepository->createIfNotExist($this->user, ['device_info.id'=> $request->device_id, 'last_login_as'=> 'guest']);
        if (!$user->guest_id) {
            throw new Exception("No guest id found to be login");
        }
        return [
            'user'=> $user,
            'credentials'=> ['guest_id'=> $user->guest_id, 'password'=> $this->password],
            'new_registration'=> $this->userRepository->getCreated()
        ];
  }
}
