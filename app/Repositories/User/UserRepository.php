<?php

namespace App\Repositories\User;

use App\Models\v1\User;
use App\Models\v1\WidgetItem;
use App\Models\v2\AgentComplementary;
use App\Models\v2\HuntStatistic;
use App\Repositories\RelicRepository;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Collection;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class UserRepository implements UserRepositoryInterface
{
	
    protected $user;
    protected $model;
    public function __construct($user)
    {
        $this->user = $user;
        $this->model = new User;
    }

	public function addSkeletonKeys(int $keysAmount, $additionalFields = null){

        for ($i=0; $i < $keysAmount; $i++) { 
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

    public function deductSkeletonKeys(int $keysAmount){

        $user = User::where('_id', $this->user->id)->select('_id', 'skeleton_keys')->first();
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

    public function addWidgetItem(WidgetItem $widgetItem)
    {
        $status = User::where('_id',$this->user->id)
                ->where('widgets.id', '!=', $widgetItem->id)
                ->push(['widgets'=> ['id'=> $widgetItem->id, 'selected'=> false]]);
        return $widgetItem->id;
    }

    public function addWidgetItems(WidgetItem $widgetItem)
    {
        $totalItems = $widgetItem->items;
        array_push($totalItems, $widgetItem->id);

        foreach ($totalItems as $item) {
            User::where('_id',$this->user->id)
            ->where('widgets.id', '!=', $item)
            ->push(['widgets'=> ['id'=> $item, 'selected'=> false]]);
        }
        return $totalItems;
    }

    public function resetWidgets(WidgetItem $widgetItem)
    {
        // if ($widgetItem->avatar->gender == 'female') {
        //     if ($widgetItem->id != "5d246f230b6d7b1a0a232482") {
        //         $widgetToRemove = WidgetItem::where('_id', "5d246f230b6d7b1a0a232482")->first();
        //         foreach ($widgetToRemove->items as $item) {
        //             User::where('_id', $this->user->id)->pull(['widgets'=> ['id'=> $item]]);
        //         }
        //         User::where('_id', $this->user->id)->pull('widgets', ['id'=> "5d246f230b6d7b1a0a232482"]);
        //     }
        // }else if ($widgetItem->avatar->gender == 'male') {
        //     if ($widgetItem->id != "5d246f0c0b6d7b19fb5ab590") {
        //         $widgetToRemove = WidgetItem::where('_id', "5d246f0c0b6d7b19fb5ab590")->first();
        //         foreach ($widgetToRemove->items as $item) {
        //             User::where('_id', $this->user->id)->pull('widgets', ['id'=> $item]);
        //         }
        //         User::where('_id', $this->user->id)->pull('widgets', ['id'=> "5d246f0c0b6d7b19fb5ab590"]);
        //     }
        // }

        // remove female outfit from user's account
        if ($widgetItem->id != "5d246f230b6d7b1a0a232482") {
            $widgetToRemove = WidgetItem::where('_id', "5d246f230b6d7b1a0a232482")->first();
            foreach ($widgetToRemove->items as $item) {
                User::where('_id', $this->user->id)->pull(['widgets'=> ['id'=> $item]]);
            }
            User::where('_id', $this->user->id)->pull('widgets', ['id'=> "5d246f230b6d7b1a0a232482"]);
        }

        // remove male outfit from user's account
        if ($widgetItem->id != "5d246f0c0b6d7b19fb5ab590") {
            $widgetToRemove = WidgetItem::where('_id', "5d246f0c0b6d7b19fb5ab590")->first();
            foreach ($widgetToRemove->items as $item) {
                User::where('_id', $this->user->id)->pull('widgets', ['id'=> $item]);
            }
            User::where('_id', $this->user->id)->pull('widgets', ['id'=> "5d246f0c0b6d7b19fb5ab590"]);
        }

        // add the selected widget
        $totalItems = $widgetItem->items;
        array_push($totalItems, $widgetItem->id);
        foreach ($totalItems as $item) {
            User::where('_id',$this->user->id)
            ->where('widgets.id', '!=', $item)
            ->push(['widgets'=> ['id'=> $item, 'selected'=> true]]);
        }

        // add the equivalent widget of selected widget
        $equivalentOutfit = WidgetItem::where('_id', $widgetItem->similar_outfit)->first();
        // $equivalentItems = $this->addWidgetItems($equivalentOutfit);
        $equivalentItems = $equivalentOutfit->items;
        array_push($equivalentItems, $equivalentOutfit->id);
        foreach ($equivalentItems as $item) {
            User::where('_id',$this->user->id)
            ->where('widgets.id', '!=', $item)
            ->push(['widgets'=> ['id'=> $item, 'selected'=> true]]);
        }

        if ($this->user->gender == 'female') {
            $widgets[] = '5d4424455c60e6147cf181b4';
        }else{
            $widgets[] = '5d4423d65c60e6147cf181a6';
        }
        // return outfits
        return array_merge($totalItems, $equivalentItems, $widgets);
        // throw new Exception("Invalid avatar type provided.");
    }

    public function markTutorialAsComplete(string $identifier)
    {
        return User::where(['_id'=> $this->user->id])->update(['tutorials.'.$identifier=> new UTCDateTime(now()) ]);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setAgentStatus($points = null, $level = null)
    {
        $this->user->agent_status = [
            'xp'=> ($points)? ($this->user->agent_status['xp'] + $points): $this->user->agent_status['xp'],
            'level'=> ($level)?  ($this->user->agent_status['level'] + $level): $this->user->agent_status['level'],
        ];
        return $this;
    }

    public function addXp($points)
    {
        if ($this->powerFreezeTill() > 0) {
            $points *= 2;
        }
        $this->model->where('_id',$this->user->id)->increment('agent_status.xp', $points);
        $this->setAgentStatus($points);
        return $this->user->agent_status['xp'];
    }

    public function allotAgentLevel($levelToBeIncrement)
    {
        $this->model->where('_id',$this->user->id)->increment('agent_status.level', $levelToBeIncrement);
        $this->setAgentStatus(null, $levelToBeIncrement);
        return $this->user->agent_status['level'];
    }

    public function addWidgets(Collection $ids)
    {
        $ids->map(function($id) {
            $this->user->push('widgets', ['id'=> $id, 'selected'=> false]);
            return $id;
        });
        return $ids;
    }
    
    // public function addRelic($relicId)
    // {
    //     return $this->user->push('relics', $relicId, true);
    // }

    public function addRelic($relicId)
    {
        // \DB::connection()->enableQueryLog();
        // $this->user->push('relics', ['id'=> $relicId, 'status'=> false]);
        // $queries = \DB::getQueryLog();
        // dd($this->user->relics);
        $status = $this->model->where('_id', $this->user->id)->where('relics.id', '!=', $relicId)->push('relics', ['id'=> $relicId, 'status'=> false]);
        (new RelicRepository)->setRelicId($relicId)->addUser($this->user->id);
        return ['_id'=> $relicId, 'status'=> false];
    }

    public function activateTheRelic($relicId)
    {
        $status = $this->model->where(['_id'=> $this->user->id, 'relics.id'=> $relicId, 'relics.status'=> false])->update(['relics.$.status'=> true]);
        if ($status) {
            return ['_id'=> $relicId, 'status'=> true];
        }else{
            throw new Exception("Relic cannot be activate.");
        }
    }

    public function addPower(int $power)
    {
        if(($this->user->power_status['power'] + $power) >= 100) {
            // $this->user->power_status = ['power'=> 100, 'full_peaked_at'=> new UTCDateTime(now()), 'activated'=> false];
            $this->user->power_status = ['power'=> 100, 'full_peaked_at'=> new UTCDateTime(now())];
        }else if(($this->user->power_status['power'] + $power) < 100) {
            $this->user->power_status = ['power'=> ($this->user->power_status['power'] + $power)];
        }
        $this->user->save();
        return $this->user->power_status;
    }

    public function getAgentStack()
    {
        $agentLevels = AgentComplementary::whereIn('agent_level', [$this->user->agent_status['level'], $this->user->agent_status['level']+1])
                        ->orderBy('agent_level', 'asc')
                        ->select('_id', 'agent_level', 'xps')
                        ->get();

        return ['current'=> $agentLevels->first(), 'upcoming'=> $agentLevels->last()];
    }

    public function powerFreezeTill()
    {
        // $userBoostedAt = (isset($this->user->power_status['full_peaked_at']))?CarbonImmutable::parse($this->user->power_status['full_peaked_at']): false;
        // if ($userBoostedAt) {
        //     $freezeThePowerTill = HuntStatistic::select('_id', 'boost_power_till')->first();
        //     $remainingFreezePowerTill = $userBoostedAt->addSeconds($freezeThePowerTill->boost_power_till);
        //     $remainingFreezePowerTime = ($remainingFreezePowerTill->gte(now()))? $remainingFreezePowerTill->diffInSeconds(now()): 0;
        // }
        // return $remainingFreezePowerTime ?? 0;
        if (isset($this->user->power_status['full_peaked_at']) && isset($this->user->power_status['activated_at'])) {
            $userBoostedAt = CarbonImmutable::parse($this->user->power_status['activated_at']);
            $freezeThePowerTill = HuntStatistic::select('_id', 'boost_power_till')->first();
            $remainingFreezePowerTill = $userBoostedAt->addSeconds($freezeThePowerTill->boost_power_till);
            $remainingFreezePowerTime = ($remainingFreezePowerTill->gte(now()))? $remainingFreezePowerTill->diffInSeconds(now()): 0;
        }
        return $remainingFreezePowerTime ?? 0;
    }

    public function streamingRelic()
    {
        $userRelics = User::find($this->user->id, ['_id', 'relics'])->relics;
        $relic = (new RelicRepository)->getModel()->when(($userRelics->count() > 0), function($query) use ($userRelics) {
                    $query->whereNotIn('_id', $userRelics->pluck('id')->toArray());
                })
                ->active()
                ->orderBy('number', 'asc')
                ->select('_id', 'name', 'number', 'active', 'pieces')
                ->first();
        // $relic = (new RelicRepository)->getModel()->when(($this->user->relics->count() > 0), function($query) {
        //             $query->whereNotIn('_id', $this->user->relics->pluck('id')->toArray());
        //         })
        //         ->active()
        //         // ->orderBy('created_at', 'asc')
        //         ->orderBy('number', 'asc')
        //         ->select('_id', 'name', 'number', 'active', 'pieces')
        //         ->first();

        if ($relic) {
            $relic->collected_pieces = $relic->hunt_users_reference()->where(['status'=> 'completed', 'user_id'=> $this->user->id])->count();
        }
        return $relic;
    }

    public function activateThePower()
    {
        $updatedPowerStatus = $this->user->power_status;
        $updatedPowerStatus['power'] = 0;
        $updatedPowerStatus['activated_at'] = new UTCDateTime();
        $this->user->power_status = $updatedPowerStatus;
        $this->user->save();
        return array_merge($this->user->power_status, ['till'=> $this->powerFreezeTill()]);
    }
}