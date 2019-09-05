<?php

namespace App\Repositories\User;

use App\Models\v1\User;
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

	public function addSkeletonKeys(int $keysAmount, $additionalFields = null){

        for ($i=0; $i < $keysAmount; $i++) { 
            // $skeletonKey[$i]['key'] = [ 'key'=> new ObjectId(), 'created_at'=> new UTCDateTime(), 'used_at'=> null ];
            if ($additionalFields) {
                $skeletonKey[$i] = $additionalFields;
            }
            $skeletonKey[$i]['key'] = new ObjectId();
            $skeletonKey[$i]['created_at'] = new UTCDateTime();
            $skeletonKey[$i]['used_at'] = null;
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

    public function deductGold(int $goldAmount){
        $this->user->gold_balance -= $goldAmount;
        $this->user->save();
        
        return $this->user->gold_balance;
    }

    public function addSkeletonsBucket(int $size)
    {
        $this->user->skeletons_bucket += $size;
        $this->user->save();
        return $this->user->skeletons_bucket;
    }
    // public function expandTheSkeletonBucket($keysAmount)
    // {
    //     $this->user->expnadable_skeleton_keys += $keysAmount;
    //     $this->user->save();
    // }

    public function deductSkeletonKeys(int $keysAmount){

        $user = User::where('_id', $this->user->_id)->select('_id', 'skeleton_keys')->first();
        $skeletonToBeUpdate = collect($user->skeleton_keys)->where('used_at', null)->take($keysAmount)->pluck('key');
        User::where('_id',$user->id)
            ->update(
                [ 'skeleton_keys.$[identifier].used_at'=> new UTCDateTime(now()) ],
                [ 'arrayFilters'=> [ [ "identifier.key"=> ['$in'=> $skeletonToBeUpdate->toArray()] ] ], 'new'=> true ]
            );
        return $user->available_skeleton_keys - 1;
    }

    public function markMiniGameTutorialAsComplete(string $gameId)
    {
        return User::where(['_id'=> $this->user->id, 'minigame_tutorials.game_id'=> $gameId])
                    ->update(['minigame_tutorials.$.completed_at'=> new UTCDateTime(now()) ]);
    }
}