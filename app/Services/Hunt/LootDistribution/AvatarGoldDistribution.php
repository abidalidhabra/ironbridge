<?php

namespace App\Services\Hunt\LootDistribution;

use App\Services\Hunt\LootDistribution\LootTrait;
use App\Services\Hunt\LootDistribution\WidgetDistribution;

class AvatarGoldDistribution
{
	use LootTrait;

	public function open()
	{
		$widget = (new WidgetDistribution)->setWidgets($this->loot->widgets_order)->setUser(auth()->user())->spin()->open();
		$this->setResponse(
			array_merge(['golds'=> $this->userRepository->addGold($this->loot->gold_value)], $widget->getResponse())
		);
		return $this;
	}
}