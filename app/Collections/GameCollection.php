<?php

namespace App\Collections;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;


class GameCollection extends Collection
{

	protected $user;
	
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}

	public function loadTreasureNodesTargets()
	{
		return $this->each(function ($game) {
			$game->load('treasure_nodes_target');
		});
	}

	public function addRemainingSeconds()
	{
		return $this->each(function ($game) {
			if ($completedAt = $this->user->mgc_status->where('game_id', $game->id)->first()['completed_at']) {
				$completedAt = Carbon::parse($completedAt)->addHours(4);
            	$remainingFreezeTime = ($completedAt->gte(now()))? $completedAt->diffInSeconds(now()): 0;
			}
			$game->reamining_seconds = $remainingFreezeTime ?? 0;
		});
	}
}
