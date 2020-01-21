<?php

namespace App\Services\Hunt;

use App\Repositories\HuntStatisticRepository;
use App\Services\Traits\UserTraits;
use App\Services\User\AddXPService;

class ChestRewardsService
{
	use UserTraits;

	public $xp;
	public $xPDistributionService;
	
	public function __construct()
	{
		$this->xp = (new HuntStatisticRepository)->first(['_id', 'chest_xp'])->chest_xp ?? 0;
	}

	public function get()
	{
        return [
        	'xp_provided'=> $this->xp,
        	'xp_rewards'=> (new AddXPService)->setUser($this->user)->add($this->xp)
        ];
	}
}