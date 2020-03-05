<?php

namespace App\Services\Hunt;

use App\Models\v3\CompassesLoot;
use App\Services\AssetsLogService;
use App\Services\Event\UserEventService;
use App\Services\Hunt\LootDistribution\LootTrait;
use App\Services\User\CompassService;

class CompassesLootService
{

	use LootTrait;

	public $compassService;
	public $compasses;
	public $userEventService;
	public $event;

	public function spin()
	{
		$this->setMagicNumber(
			rand(1, 1000)
		);
		return $this;
	}

	public function canCredit()
	{
		$compassesAfterCredit = $this->userEventService->thisWeekEarnedCompasses() + $this->loot->compasses;
		if ($compassesAfterCredit > $this->event->weekly_max_compasses) {
			return false;
		}else{
			return true;
		}
	}

	public function open()
	{

        (new AssetsLogService('compass', 'loot'))->setUser($this->user)->compasses($this->loot->compasses)->save();

		$this->compassService = (new CompassService)->setUser($this->user);

		$this->setResponse(
			$this->compassService->add($this->loot->compasses)->response()
		);

		return $this;
	}

	public function generate()
	{

		$this->userEventService = (new UserEventService)->setUser($this->user);
		
		$this->setLoot(
			CompassesLoot::where('min', '<=', $this->magicNumber)->where('max','>=',$this->magicNumber)->first()
		);

		if ($this->event = $this->userEventService->running()) {
			if ($this->canCredit()) {
				$this->open();
			}
		}
		
		return $this;
	}
}