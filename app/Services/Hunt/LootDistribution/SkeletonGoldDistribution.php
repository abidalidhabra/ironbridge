<?php

namespace App\Services\Hunt\LootDistribution;

use App\Services\Hunt\LootDistribution\LootTrait;

class SkeletonGoldDistribution
{
	use LootTrait;

	public function open()
	{
		$this->setResponse([
			'golds'=> $this->userRepository->addGold($this->loot->gold_value),
			'skeleton_keys'=> $this->userRepository->addSkeletonKeys($this->loot->skeletons)
		]);
		return $this;
	}
}