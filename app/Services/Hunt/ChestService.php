<?php

namespace App\Services\Hunt;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\ChestRewardsService;
use App\Services\Hunt\LootDistribution\OldLootService;
use App\Services\Hunt\RelicService;
use App\Services\MiniGame\MiniGameInfoService;
use App\Services\Traits\UserTraits;
use GuzzleHttp\Client;

class ChestService
{
	use UserTraits;

	protected $chestRewards;
	protected $userBuckets;
	protected $lootRewards = [];
	protected $bucketRestored = false;
	protected $relicInfo;

    public function expand($amount)
    {
    	$this->setUserBuckets(
			$this->user->buckets
		);

		$this->userBuckets['chests']['capacity'] += $amount;
		$this->userBuckets['chests']['remaining'] += $amount;
		$this->save();
    }

	public function add()
	{
		$this->setUserBuckets(
			$this->user->buckets
		);

		// if ($this->userBuckets['chests']['collected'] >= $this->userBuckets['chests']['capacity']) {
		// 	throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
		// }else{
		// 	$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
		// 	$this->userBuckets['chests']['collected'] += 1;
		// 	$this->userBuckets['chests']['remaining'] -= 1;
		// 	$this->save();
		// }
		
		$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
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
		
		$this->userBuckets['chests']['remaining'] += 1;

		$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;
		
		$this->save();

        $this->setLootRewards(
        	(new OldLootService)->setUser($this->user)->generate()
        );        

        $this->setChestRewards(
        	(new ChestRewardsService)->setUser($this->user)->get()
        );

        $this->setRelicInfo(
        	(new RelicService)->setUser($this->user)->addMapPiece()
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

		$this->cutTheCharge();
		
		$this->save();
	}

	public function cutTheCharge()
	{
		$huntStatistic = (new HuntStatisticRepository)->first(['id', 'mg_change_charge']);
		// if ($user->gold_balance >= $huntStatistic->retention_hunt['refresh_mg_charge']) {
			return (new UserRepository($this->user))->deductGold($huntStatistic->mg_change_charge ?? 1);
		// }
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

    public function response()
    {
        return [
            'xp_state'=> $this->getChestRewards(),
            'next_minigame'=> $this->getMiniGame(),
            'loot_rewards'=> $this->getLootRewards(),
            'chests_bucket'=> $this->user->buckets['chests'],
            'relic_info'=> $this->getRelicInfo()
        ];
    }
}