<?php

namespace App\Services\Hunt\LootDistribution;

use App\Services\Hunt\LootDistribution\LootTrait;

class GoldDistribution
{
	use LootTrait;

	public function open()
	{
		$this->gold();
		return $this;
	}
}