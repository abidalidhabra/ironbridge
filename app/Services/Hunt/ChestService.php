<?php

namespace App\Services\Hunt;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\LootDistribution\OldLootService;
use App\Services\MiniGame\MiniGameInfoService;
use App\Services\Traits\UserTraits;
use GuzzleHttp\Client;

class ChestService
{
	use UserTraits;

	protected $userBuckets;
	protected $lootRewards = [];
	protected $bucketRestored = false;

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

	public function changeChestMiniGame()
	{

		$this->setUserBuckets(
			$this->user->buckets
		);

		$this->userBuckets['chests']['minigame_id'] = (new GetRandomizeGamesService)->setUser($this->user)->first(null, [$this->userBuckets['chests']['minigame_id']])->id;

		$this->cutTheCharge();
		
		$this->save();
	}

	public function cutTheCharge()
	{
		$huntStatistic = (new HuntStatisticRepository)->first(['id', 'retention_hunt']);
		// if ($user->gold_balance >= $huntStatistic->retention_hunt['refresh_mg_charge']) {
			return (new UserRepository($this->user))->deductGold($huntStatistic->retention_hunt['refresh_mg_charge'] ?? 1);
		// }
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
}