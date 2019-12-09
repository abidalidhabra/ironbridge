<?php

namespace App\Services\User\Authentication;

use App\Services\User\Authentication\AppleLogin;
use App\Services\User\Authentication\FacebookLogin;
use App\Services\User\Authentication\GoogleLogin;
use App\Services\User\Authentication\GuestLogin;

class LoginFactory
{
	public function init($type)
	{
		if ($type == 'google') {
			return new GoogleLogin;
		}else if ($type == 'facebook') {
			return new FacebookLogin;
		}else if ($type == 'apple') {
			return new AppleLogin;
		}else if ($type == 'guest') {
			return new GuestLogin;
		}
		throw new Exception("Invalid login type provided.");
	}
}