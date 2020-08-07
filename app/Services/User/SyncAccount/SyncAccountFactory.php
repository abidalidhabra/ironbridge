<?php

namespace App\Services\User\SyncAccount;

use App\Services\User\SyncAccount\ToEmailAccount;
use Exception;

class SyncAccountFactory
{
	public function init($type, $user, $request)
	{
		if ($type == 'email') {
			return new ToEmailAccount($user, $request);
		}
		throw new Exception("Invalid sync type provided.");
	}
}