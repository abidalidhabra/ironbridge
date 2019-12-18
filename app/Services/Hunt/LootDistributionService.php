<?php

namespace App\Services\Hunt;

class LootDistributionService
{
	
	public function __construct(LootRepo)
	{
		$this->paybleGoogleURL = 'https://playablelocations.googleapis.com/v3:searchPlayableLocations?key='.$this->paybleGoogleKey;
	}

}