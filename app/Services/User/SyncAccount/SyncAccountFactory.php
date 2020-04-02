<?php

namespace App\Services\User\SyncAccount;

use App\Services\User\SyncAccount\ToEmailAccount;
use Exception;

class SyncAccountFactory
{
	public function init($type)
	{
		if ($type == 'email') {
			return new ToEmailAccount;
		}
		throw new Exception("Invalid sync type provided.");
	}
}