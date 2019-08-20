<?php
namespace App\Repositories\HuntReward;

class Reward
{
	protected $user; 

	function __construct($user)
	{
		$this->user = $user;
	}
}