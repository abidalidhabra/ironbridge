<?php

namespace App\Repositories;

/**
 * user clue repository
 */
class GenerateReward
{
	
	function __construct()
	{
	}

	public function generateReward()
	{
        $randNumber  = rand(1, 1000);
        $huntUser    = $huntUserDetail->hunt_user()->select('complexity','user_id')->first();
        $complexity  = $huntUser->complexity;
        $user        = auth()->user();
        $userId      = $user->_id;
        $userGender  = ($user->avatar_detail)?$user->avatar_detail->gender:'male';
        $rewards     = HuntReward::all();

        $selectedReward = $rewards->where('complexity',$complexity)
        					->where('min_range', '<=', $randNumber)
        					->where('max_range','>=',$randNumber)
        					->firstOrFail();
        
        $rewardData['random_number'] = $randNumber;
        
        if ($selectedReward->widgets_order && is_array($selectedReward->widgets_order)) {
            
            $widgetOrder     = $selectedReward->widgets_order;
            
            findWidget:
            $countableWidget = $widgetOrder[0];
            $widgetCategory  = $countableWidget['type'];
            $widgetName      = $countableWidget['widget_name'];

            $userWidgets = collect($user->widgets)->pluck('id');
            $widgetItems = WidgetItem::when($widgetCategory != 'all', function ($query) use ($widgetCategory){
                                return $query->where('widget_category', $widgetCategory);
                            })
                            ->havingGender($userGender)
                            ->where('widget_name',$widgetName)
                            ->whereNotIn('_id',$userWidgets)
                            ->select('_id', 'widget_name', 'avatar_id', 'widget_category')
                            ->first();

            if (!$widgetItems) {
                $widgetOrder = array_splice($widgetOrder, 1);
                if (count($widgetOrder) == 0) { goto distSkeleton; }
                goto findWidget; 
            }

            $user->push('widgets', ['id'=> $widgetItems->id, 'selected'=> false]);
            $message[] = 'Widget has been unlocked';
            $rewardData['widget'] = $widgetItems;
        }

        if ($selectedReward->skeletons){
            distSkeleton:
            $skeletons = [];
            for ($i=0; $i < $selectedReward->skeletons; $i++) { 
                $skeletons[] = [
                    'key'       => strtoupper(substr(uniqid(), 0, 10)),
                    'created_at'=> new MongoDBDate() ,
                    'used_at'   => null
                ];
            }
            $user->push('skeleton_keys', $skeletons);
            $message[] = 'Skeleton key provided';
            $rewardData['skeleton_keys'] = $skeletons;
        }

        if ($selectedReward->gold_value){
            distGold:
            $user->gold_balance += $selectedReward->gold_value;
            $user->save();
            $message[] = 'Gold provided.';
            $rewardData['golds'] = $selectedReward->gold_value;
        }

        unset($selectedReward->min_range, $selectedReward->max_range);
        return [ 'reward_messages' => implode(',', $message), 'reward_data' => $rewardData];
	}
}