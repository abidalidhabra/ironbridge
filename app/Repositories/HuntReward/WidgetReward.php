<?php

namespace App\Repositories\HuntReward;

use App\Models\v1\WidgetItem;
use App\Repositories\HuntReward\Reward;
use App\Repositories\HuntReward\SkeletonKeyReward;
use App\Repositories\User\UserRepository;

class SkeletonKeyReward extends Reward
{

	public function injectReward($rewardData)
	{
		/** Get the priority widget first **/
		$widgetOrders = collect($rewardData->widgets_order);
        $userGender   = ($this->user->avatar_detail)?$user->avatar_detail->gender:'male';

		findWidget:
		$widget = $widgetOrders->first();

		/** Widgets that dont need **/
		$userWidgets = collect($this->user->widgets)->pluck('id');
		
		$widgetItems = WidgetItem::when($widget['type'] != 'all', function ($query) use ($widget){
							return $query->where('widget_category', $widget['type']);
						})
						->havingGender($userGender)
						->where('widget_name',$widget['widget_name'])
						->whereNotIn('_id',$userWidgets)
						->select('_id', 'widget_name', 'avatar_id', 'widget_category')
						->first();

		if (!$widgetItems) {
			$widgetOrders->forget(0)->values();
			if (!$widgetOrders->count()) { 
				$skeletonKeyReward = new SkeletonKeyReward($this->user);
				$skeletonKeyReward->injectReward(1);
			}else{
				goto findWidget; 
			}
		}else{
			$this->user->push('widgets', ['id'=> $widgetItems->id, 'selected'=> false]);
		}

		return $this->user;
	}
}