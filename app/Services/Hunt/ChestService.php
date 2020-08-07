<?php

namespace App\Services\Hunt;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Models\v3\JokeItem;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\ChestRewardsService;
use App\Services\Hunt\CompassesLootService;
use App\Services\Hunt\LootDistribution\OldLootService;
use App\Services\Hunt\RelicService;
use App\Services\MiniGame\MiniGameInfoService;
use App\Services\Traits\UserTraits;
use Exception;
use GuzzleHttp\Client;

class ChestService
{
	use UserTraits;

	protected $chestRewards;
	protected $userBuckets;
	protected $lootRewards = [];
	protected $bucketRestored = false;
	protected $relicInfo;
    protected $compassRewards;
    protected $availableSkeletonKeys;
    protected $jokeItem;

    public function expand($amount)
    {
    	$this->setUserBuckets(
			$this->user->buckets
		);

		$this->userBuckets['chests']['capacity'] += $amount;
		$this->userBuckets['chests']['remaining'] += $amount;
		$this->save();
    }

	public function add($placeId)
	{
		$this->setUserBuckets(
			$this->user->buckets
		);

        // if ($this->userBuckets['chests']['collected'] + 1 > $this->userBuckets['chests']['capacity']) {
        //     throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
        // }

		// if ($this->userBuckets['chests']['collected'] >= $this->userBuckets['chests']['capacity']) {
		// 	throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
		// }else{
		// 	$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
		// 	$this->userBuckets['chests']['collected'] += 1;
		// 	$this->userBuckets['chests']['remaining'] -= 1;
		// 	$this->save();
		// }
		
        if (!isset($this->userBuckets['chests']['minigame_id'])) {
		  $this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
        }
        
        $this->markThisChestAsTaken($placeId);

		$this->userBuckets['chests']['collected'] += 1;
		$this->userBuckets['chests']['remaining'] -= 1;
		$this->save();
	}

	public function open()
	{
		$this->setUserBuckets(
			$this->user->buckets
		);
		
		$this->userBuckets['chests']['collected'] -= 1;
		
		$this->userBuckets['chests']['remaining'] -= 1;

		$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
		
		$this->save();

        $this->setLootRewards(
        	(new OldLootService)->setUser($this->user)->generate()
        );        

        $this->setChestRewards(
        	(new ChestRewardsService)->setUser($this->user)->get()
        );

        /** Relic Map Pieces **/
        $magicNumber = rand(1, 100);
        $huntStatistic = (new HuntStatisticRepository)->first(['id', 'map_pieces', 'joke_item']);
        if ($magicNumber <= $huntStatistic->map_pieces['max']) {
            $this->setRelicInfo(
            	(new RelicService)->setUser($this->user)->addMapPiece()
            );
        }

        /** Joke Item **/
        $magicNumber = rand(1, 100);
        if ($magicNumber <= $huntStatistic->joke_item['max']) {
            $this->setJokeItem(
                JokeItem::first()
            );
        }

        $this->setCompassRewards(
            (new CompassesLootService)->setUser($this->user)->spin()->generate()
        );

		return $this;
	}

	public function save()
	{
		$this->user->buckets = $this->userBuckets;
		$this->user->save();
	}

	public function generateMiniGame()
	{
		return (new GetRandomizeGamesService)->setUser($this->user)->first();
	}

	public function getMiniGame()
	{
		
		$this->setUserBuckets(
			$this->user->buckets
		);

		// return (new GetRandomizeGamesService)
		// 		->setUser($this->user)
		// 		->first(
		// 			$this->userBuckets['chests']['minigame_id'] ?? null
		// 		);
		return (new MiniGameInfoService)->setUser($this->user)->chestMiniGame();
	}

	public function changeChestMiniGame($MGIds)
	{

		$this->setUserBuckets(
			$this->user->buckets
		);

        $this->userBuckets['chests']['minigame_id'] = (new GetRandomizeGamesService)->setUser($this->user)->first(
            null, $MGIds
        )->id;

		$this->cutTheCharge('minigame_change');
		
		$this->save();
	}

	public function cutTheCharge($for)
	{
        $huntStatistic = (new HuntStatisticRepository)->first(['id', 'chest']);
        $userRepository = new UserRepository($this->user);
        if ($for == 'skipping_chest') {
            return $this->availableSkeletonKeys = $userRepository->deductSkeletonKeys($huntStatistic->chest['skeleton_keys_to_skip'] ?? 1);
        }else if($for == 'minigame_change'){
    		return $userRepository->deductGold($huntStatistic->chest['golds_to_skip_mg'] ?? 1);
        }
        throw new Exception("Invalid type provided to cutting charge on behalf of chest");
	}

	public function remove()
    {
    	$this->setUserBuckets(
			$this->user->buckets
		);

		$this->userBuckets['chests']['collected'] -= 1;
		$this->userBuckets['chests']['remaining'] += 1;
		
		$this->save();
    }

    public function sync()
    {
    	$this->setUserBuckets(
			$this->user->buckets
		);

    	if ($this->userBuckets['chests']['collected'] > $this->userBuckets['chests']['capacity']) {
			$this->userBuckets['chests']['collected'] = $this->userBuckets['chests']['capacity'];
			$this->userBuckets['chests']['remaining'] = 0;
			$this->save();
			$this->setBucketRestored(true);
    	}
    	return $this;
    }

	/**
     * @param mixed $userBuckets
     *
     * @return self
     */
    public function setUserBuckets($userBuckets)
    {
        $this->userBuckets = $userBuckets;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLootRewards()
    {
        return $this->lootRewards;
    }

    /**
     * @param mixed $lootRewards
     *
     * @return self
     */
    public function setLootRewards($lootRewards)
    {
        $this->lootRewards = $lootRewards;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBucketRestored()
    {
        return $this->bucketRestored;
    }

    /**
     * @param mixed $bucketRestored
     *
     * @return self
     */
    public function setBucketRestored($bucketRestored)
    {
        $this->bucketRestored = $bucketRestored;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChestRewards()
    {
        return $this->chestRewards;
    }

    /**
     * @param mixed $chestRewards
     *
     * @return self
     */
    public function setChestRewards($chestRewards)
    {
        $this->chestRewards = $chestRewards;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelicInfo()
    {
        return $this->relicInfo;
    }

    /**
     * @param mixed $relicInfo
     *
     * @return self
     */
    public function setRelicInfo($relicInfo)
    {
        $this->relicInfo = $relicInfo;

        return $this;
    }

    /**
     * @param mixed $compassRewards
     *
     * @return self
     */
    public function setCompassRewards($compassRewards)
    {
        $this->compassRewards = $compassRewards;

        return $this;
    }

    public function response()
    {
        $response = $this->treasureChestLootResponse();
        if ($compassRewards = $this->compassRewards->getResponse()) {
            $response['compass_rewards'] = $this->compassRewards->getResponse();
        }
        return $response;
    }

    public function treasureChestLootResponse()
    {
        $response = [
            'xp_state'=> $this->getChestRewards(),
            'next_minigame'=> $this->getMiniGame(),
            'loot_rewards'=> $this->getLootRewards(),
            'chests_bucket'=> $this->user->buckets['chests'],
            // 'relic_info'=> $this->getRelicInfo(),
            'available_skeleton_keys'=> (is_numeric($this->availableSkeletonKeys))? $this->availableSkeletonKeys: $this->user->available_skeleton_keys,
        ];
        if ($relicInfo = $this->getRelicInfo()) {
            $response['relic_info'] = $relicInfo;
        }
        if ($jokeItem = $this->getJokeItem()) {
            $response['joke_item'] = $jokeItem;
        }
        return $response;
    }

    public function when($value, $callback)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }

        return $this;
    }

    public function markThisChestAsTaken($placeId)
    {
        // $this->user->chests()->create(['place_id'=> $placeId, 'city_id'=> $this->user->city_id]);
        $this->user->chests()->create(['place_id'=> $placeId]);
        return $this;
    }

    /**
     * @param mixed $jokeItem
     *
     * @return self
     */
    public function setJokeItem($jokeItem)
    {
        $this->jokeItem = $jokeItem;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJokeItem()
    {
        return $this->jokeItem;
    }

    public function remainingFreezeTime()
    {
        $huntStatistic = (new HuntStatisticRepository)->first(['_id', 'freeze_till']);
        $chests = $this->user->chests()
                // ->whereNotNull('city_id')
                // ->where('city_id', $this->user->city_id)
                ->groupBy('place_id')
                ->get(['created_at'])
                ->map(function($chest) use ($huntStatistic){
                    $freezeTime = $chest->created_at->addSeconds($huntStatistic->freeze_till['chest']);
                    $chest->freeze_till = ($freezeTime->gte(now()))? $freezeTime->diffInSeconds(now()): 0;
                    return $chest;
                });
        return $chests;
    }
}