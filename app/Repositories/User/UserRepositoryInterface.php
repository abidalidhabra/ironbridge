<?php

namespace App\Repositories\User;

interface UserRepositoryInterface{

	public function addSkeletonKeys(int $keysAmount, $additionalFields);
	
	public function addGold(int $goldAmount);
	
	public function buySkeletonKeysFromGold($purchaseData);
	
	public function addSkeletonsBucket(int $size);
    
    public function markMiniGameTutorialAsComplete(string $gameId);
}