<?php

namespace App\Http\Controllers\Api\Relic;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class RelicController extends Controller
{
    
    public function markTheRelicAsComplete(Request $request)
    {
    	$status = (new UserRepository(auth()->user()->id))
    				->getModel()
    				->where(['_id'=> auth()->user()->id, 'relics.id'=> $request->relic_id])
    				->where('relics.status', '!=', true)
    				->update(['relics.$.status'=> true]);

		$XPManagementRepository = new XPManagementRepository;
		$complexity = $this->huntUser->complexity;
		$xp = $XPManagementRepository->getModel()->where(['event'=> 'clue_completion', 'complexity'=> $complexity])->first()->xp;
		if ($treasureCompleted) {
			$xp += $XPManagementRepository->getModel()->where(['event'=> 'treasure_completion', 'complexity'=> $complexity])->first()->xp;
		}
		$xpReward = $this->addXPService->add($xp);
    	return response()->json(['message'=> 'OK', 'status'=> $status]);
    }
}
