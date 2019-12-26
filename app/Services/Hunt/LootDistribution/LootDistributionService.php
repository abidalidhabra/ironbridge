<?php

namespace App\Services\Hunt\LootDistribution;

use App\Services\Hunt\LootDistribution\AvatarGoldDistribution;
use App\Services\Hunt\LootDistribution\LootTrait;
use App\Services\Hunt\LootDistribution\WidgetDistribution;
use Exception;

class LootDistributionService
{

	use LootTrait;

	public function spin()
	{
		$this->setMagicNumber(
			rand(1, 1000)
			// 802
		);

		$this->setLoot(
			$this->loots->where('min_range', '<=', $this->magicNumber)->where('max_range','>=',$this->magicNumber)->first()
		);

		return $this;
	}

	public function unbox()
	{

		if ($this->loot->reward_type == 'skeleton_key_and_gold') {
			return (new SkeletonGoldDistribution)->setLoot($this->loot);
		}else if($this->loot->reward_type == 'avatar_item_and_gold') {
			return (new AvatarGoldDistribution)->setLoot($this->loot);
		}else if($this->loot->reward_type == 'gold') {
			return (new GoldDistribution)->setLoot($this->loot);
		}else if($this->loot->reward_type == 'skeleton_key') {
			return (new SkeletonDistribution)->setLoot($this->loot);
		}else if($this->loot->reward_type == 'avatar_item') {
			return (new WidgetDistribution)->setWidgets($this->loot->widgets_order)->spin();
		}

		throw new Exception("Invalid reward type found.");
	}
}