<?php

namespace App\Helpers;

use App\Models\v1\UserBalancesheet;

class TransactionHelper {
	
	public static function makePassbookEntry($userId,$whereItHappens,$forWhatItHappens,$balanceType,$amount)
	{
		UserBalancesheet::create([
			'user_id' => $userId,
			'happens_at' => $whereItHappens,
			'happens_at' => $whereItHappens,
			'happens_because' => $forWhatItHappens,
			'balance_type'	=> $balanceType,
			'credit' 	=> $amount,
		]);
	}
}