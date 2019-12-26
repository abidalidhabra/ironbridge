<?php

namespace App\Services\Hunt\LootDistribution;

use App\Services\Hunt\LootDistribution\LootTrait;

class SkeletonDistribution
{
	use LootTrait;

	public function open()
	{
		$this->skeletons();
		return $this;
	}
}