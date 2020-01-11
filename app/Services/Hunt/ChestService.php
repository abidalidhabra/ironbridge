<?php

namespace App\Services\Hunt;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Services\Traits\UserTraits;
use GuzzleHttp\Client;

class ChestService
{
	use UserTraits;

	protected $userBuckets;
	protected $isThisLastChest = false;

	public function add()
	{
		$this->userBuckets = $this->user->buckets;
		if (($this->userBuckets['chests']['opened'] + $this->userBuckets['chests']['remaining']) >= $this->userBuckets['chests']['capacity']) {
			throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
		}else{
			$this->userBuckets['chests']['remaining'] += 1;
			$this->save();
		}
	}

	public function open()
	{
		$this->userBuckets = $this->user->buckets;
		
		$this->userBuckets['chests']['opened'] += 1;
		
		$this->isThisLastChest = ($this->userBuckets['chests']['remaining'] == 1)? true: false;
		
		$this->userBuckets['chests']['remaining'] -= 1;
		
		$this->save();
		
		return $this;
	}

	public function save()
	{
		$this->user->buckets = $this->userBuckets;
		$this->user->save();
	}

	public function minigame()
	{
		if ($this->user->buckets['chests']['remaining'] || $this->isThisLastChest) {
			return (new GetRandomizeGamesService)->setUser(auth()->user())->first();
		}else{
			return null;
		}
	}

	// public function randomHuntReward($){

 //        /** Generate Reward **/
 //        $randNumber  = rand(1, 1000);
 //        // $complexity  = $this->huntUser->complexity;
 //        $complexity  = 1;
 //        $userId      = $this->user->_id;
 //        $userGender  = ($this->user->avatar_detail)? $this->user->avatar_detail->gender: 'male';
 //        $rewards     = HuntReward::all();

 //        // $complexity  = 1;
 //        // $randNumber  = 921; // widget
 //        // $randNumber  = 986; // relic
 //        $message = [];
 //        $selectedReward = $rewards->where('complexity', $complexity)
	// 			        ->where('min_range', '<=', $randNumber)
	// 			        ->where('max_range','>=',$randNumber)
	// 			        ->first();

 //        if (!$selectedReward) {
 //            return [ 'reward_messages'=> 'No reward found.', 'reward_data'=> new stdClass() ];
 //        }

 //        $rewardData['type'] = $selectedReward->reward_type;
 //        $rewardData['hunt_user_id'] = $this->huntUser->id;
 //        $rewardData['random_number'] = $randNumber;

 //        if ($selectedReward->widgets_order && is_array($selectedReward->widgets_order)) {

 //            $widgetRandNumber    = rand(1, 1000);
 //            $widgetOrder         = collect($selectedReward->widgets_order);
 //            $countableWidget     = $widgetOrder
 //            ->where('min', '<=', $widgetRandNumber)
 //            ->where('max','>=',$widgetRandNumber)
 //            ->first();
            
 //            findWidget:
 //            $widgetCategory  = $countableWidget['type'];
 //            $widgetName      = $countableWidget['widget_name'];

 //            $userWidgets = collect($this->user->widgets)->pluck('id');
 //            $widgetItems = WidgetItem::when($widgetCategory != 'all', function ($query) use ($widgetCategory){
 //                return $query->where('widget_category', $widgetCategory);
 //            })
 //            ->havingGender($userGender)
 //            ->where('widget_name',$widgetName)
 //            ->whereNotIn('_id',$userWidgets)
 //            ->select('_id', 'widget_name', 'avatar_id', 'widget_category')
 //            ->first();
            
 //            if (!$widgetItems) {

 //                $widgetOrder = $widgetOrder->reject(function($order) use ($widgetName, $widgetCategory) { 
 //                    return ($order['widget_name'] == $widgetName && $order['type'] == $widgetCategory); 
 //                });

 //                $countableWidget = $widgetOrder
 //                ->where('min', '!=', 0)
 //                ->where('max', '!=', 0)
 //                // ->sortByDesc('possibility')
 //                ->first();

 //                if ($widgetOrder->count() == 0) {
 //                    $rewardData['type'] = 'skeleton_key';
 //                    $selectedReward->skeletons = 1;
 //                    goto distSkeleton; 
 //                }
 //                goto findWidget; 
 //            }

 //            $widget = [ 'id'=> $widgetItems->id, 'selected'=> false ];
 //            $this->user->push('widgets', $widget);
 //            $message[] = 'Widget has been unlocked';
 //            $rewardData['widget'] = $widgetItems;
 //        }

 //        if ($selectedReward->skeletons){
 //            distSkeleton:
 //            $skeletons = [];
 //            for ($i=0; $i < $selectedReward->skeletons; $i++) { 
 //                $skeletons[] = [
 //                    'key'       => strtoupper(substr(uniqid(), 0, 10)),
 //                    'created_at'=> new UTCDateTime(),
 //                    'used_at'   => null
 //                ];
 //            }
 //            $this->user->push('skeleton_keys', $skeletons);
 //            $message[] = 'Skeleton key provided';
 //            $rewardData['skeleton_keys'] = count($skeletons);
 //        }

 //        if ($selectedReward->gold_value){
 //            distGold:
 //            $this->user->gold_balance += $selectedReward->gold_value;
 //            $this->user->save();
 //            $message[] = 'Gold provided.';
 //            $rewardData['golds'] = $selectedReward->gold_value;
 //        }

 //        $rewardData['user_id'] = $userId;
 //        (new LogTheHuntRewardService)->add($rewardData);
 //        unset($selectedReward->min_range, $selectedReward->max_range);
 //        unset($rewardData['hunt_user_id'], $rewardData['user_id'], $rewardData['type']);
 //        Log::info([ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData]);
 //        return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
 //    }

}