<?php

namespace App\Repositories;

use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Exception;

class UserRepository
{
	
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

	public function addSkeletonKeyInAccount($keysAmount){

        if (!$keysAmount) {
            throw new Exception("Zero skeleton can not be credit to an account.");
        }

		for ($i=0; $i < $keysAmount; $i++) { 
            $skeletonKey[] = [ 'key'=> new MongoDBId(), 'created_at'=> new MongoDBDate(), 'used_at'=> null ];
        }
    	$this->user->push('skeleton_keys', $skeletonKey);

    	return $this->user->available_skeleton_keys;
	}

    public function addTheGoldInAccount($goldAmount){

        $this->user->gold_balance += $goldAmount;
        $this->user->save();
        
        return $this->user->gold_balance;
    }

    public function buySkeletonFromGold($purchaseData){

        $goldToBeDeduct = $purchaseData->gold_value;
        $keysAmount     = $purchaseData->keys;
        if ($this->user->gold_balance < $goldToBeDeduct) {
            throw new Exception("You don't have enough gold balance to get skeleton keys.");
        }

        $this->user->gold_balance -= $goldToBeDeduct;
        $this->user->save();

        $availableSkeletonKeys = $this->addSkeletonKeyInAccount($keysAmount);
        return ['gold_balance'=> $this->user->gold_balance, 'available_skeleton_keys'=> $availableSkeletonKeys];
    }
}