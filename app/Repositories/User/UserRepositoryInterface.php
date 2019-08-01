<?php

namespace App\Repositories\User;

interface UserRepositoryInterface{

	public function addSkeletonKeys(int $keysAmount);
	
	public function addGold(int $goldAmount);
	
	public function buySkeletonKeysFromGold(object $purchaseData);
}