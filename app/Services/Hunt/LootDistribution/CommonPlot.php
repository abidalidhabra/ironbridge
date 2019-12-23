<?php

namespace App\Services\Hunt\LootDistribution;

use App\Repositories\User\UserRepository;

class CommonPlot
{
	
	public $loots;
	public $loot;
	public $user;
	public $userRepository;

	public function setLoots($loots)
	{
		$this->loots = $loots;
		return $this;
	}

	public function getLoots()
	{
		return $this->loots;
	}

	public function setLoot($loot)
	{
		$this->loot = $loot;
		return $this;
	}

	public function getLoot()
	{
		return $this->loot;
	}


	public function setUser($user)
	{
		$this->user = $user;
		$this->userRepository = new UserRepository($user);
		return $this;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function gold()
	{
		$this->setResponse(
			['golds'=> $this->userRepository->addGold($this->loot->gold_value)]
		);
	}

	public function skeletons($keys = 0)
	{
		$this->setResponse(
			['skeleton_keys'=> $this->userRepository->addSkeletonKeys($this->loot->skeletons ?? $keys)]
		);
	}
}