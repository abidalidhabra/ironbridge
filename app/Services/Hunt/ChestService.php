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

		if ($this->userBuckets['chests']['collected'] >= $this->userBuckets['chests']['capacity']) {
			throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
		}else{
			$this->userBuckets['chests']['next_minigame'] = $this->generateMiniGame()->id;
			$this->userBuckets['chests']['collected'] += 1;
			$this->userBuckets['chests']['remaining'] -= 1;
			$this->save();
		}
	}

	public function open()
	{
		$this->setUserBuckets(
			$this->user->buckets
		);
		
		$this->userBuckets['chests']['collected'] -= 1;
		
		$this->userBuckets['chests']['remaining'] += 1;

		$this->userBuckets['chests']['next_minigame'] = $this->generateMiniGame()->id;
		
		$this->save();
		
		return $this;
	}

	public function save()
	{
		$this->user->buckets = $this->userBuckets;
		$this->user->save();
	}

	public function generateMiniGame()
	{
		return (new GetRandomizeGamesService)->setUser(auth()->user())->first();
	}

	public function getMiniGame()
	{
		
		$this->setUserBuckets(
			$this->user->buckets
		);

		return (new GetRandomizeGamesService)
				->setUser(auth()->user())
				->first(
					$this->userBuckets['chests']['next_minigame'] ?? null
				);
	}
}