<?php

namespace App\Services\Hunt\LootDistribution;

use App\Repositories\User\UserRepository;

trait LootTrait
{
	
	protected $loots;
	protected $loot;
	protected $response;
	protected $user;
	protected $userRepository;
	protected $magicNumber = 0;

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

	public function pushResponse($response)
	{
		$this->response[] = $response;
		return $this;
	}

	public function setResponse($response)
	{
		$this->response = $response;
		return $this;
	}

	public function getResponse()
	{
		return $this->response;
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

	public function setMagicNumber($magicNumber)
	{
		$this->magicNumber = $magicNumber;
		return $this;
	}

	public function getMagicNumber()
	{
		return $this->magicNumber;
	}

	public function gold()
	{
		$this->userRepository->addGold($this->loot->gold_value);
		$this->setResponse(
			['golds'=> $this->loot->gold_value]
		);
	}

	public function skeletons($keys = 0)
	{
		$this->userRepository->addSkeletonKeys($keyToBeCredit = $this->loot->skeletons ?? $keys);
		$this->setResponse(
			['skeleton_keys'=> $keyToBeCredit]
		);
	}
}