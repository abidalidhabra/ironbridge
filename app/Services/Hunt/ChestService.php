<?php

namespace App\Services\Hunt;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\LootDistribution\OldLootService;
use App\Services\Traits\UserTraits;
use GuzzleHttp\Client;

class ChestService
{
	use UserTraits;

	protected $userBuckets;
	protected $lootRewards = [];

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
		if ($this->userBuckets['chests']['collected'] <= $this->userBuckets['chests']['capacity']) {
			$this->userBuckets['chests']['remaining'] -= 1;
		}
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

		return (new GetRandomizeGamesService)
				->setUser($this->user)
				->first(
					$this->userBuckets['chests']['minigame_id'] ?? null
				);
	}

	public function changeChestMiniGame()
	{

		$this->setUserBuckets(
			$this->user->buckets
		);

		$this->userBuckets['chests']['minigame_id'] = $this->generateMiniGame()->id;

		$this->cutTheCharge();
		
		$this->save();
	}

	public function cutTheCharge()
	{
		$huntStatistic = (new HuntStatisticRepository)->first(['id', 'retention_hunt']);
		return (new UserRepository($this->user))->deductGold($huntStatistic->retention_hunt['refresh_mg_charge'] ?? 1);
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
		
		$this->save();
    }
}