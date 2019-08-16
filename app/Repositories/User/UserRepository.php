<?php

namespace App\Repositories\User;

use App\Repositories\User\UserRepositoryInterface;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class UserRepository implements UserRepositoryInterface
{
	
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

	public function addSkeletonKeys(int $keysAmount){

        for ($i=0; $i < $keysAmount; $i++) { 
            $skeletonKey[] = [ 'key'=> new ObjectId(), 'created_at'=> new UTCDateTime(), 'used_at'=> null ];
        }
        $this->user->push('skeleton_keys', $skeletonKey);

        return $this->user->available_skeleton_keys;
	}

    public function addGold(int $goldAmount){

        $this->user->gold_balance += $goldAmount;
        $this->user->save();
        
        return $this->user->gold_balance;
    }

    public function buySkeletonKeysFromGold($purchaseData){

        $this->user->gold_balance -= $purchaseData->gold_value;
        $this->user->save();

        $availableSkeletonKeys = $this->addSkeletonKeys($purchaseData->keys);
        return ['gold_balance'=> $this->user->gold_balance, 'available_skeleton_keys'=> $availableSkeletonKeys];
    }
}