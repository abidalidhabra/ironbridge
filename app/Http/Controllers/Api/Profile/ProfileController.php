<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Repositories\HuntRewardDistributionHistoryRepository;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getRelics(Request $request)
    {
        $user = auth()->user();
        $goldEarned = (new HuntRewardDistributionHistoryRepository)->getModel()->where(['type'=> 'gold', 'user_id'=> $user->id])->sum('golds');
        $kmWalked = $user->hunt_user_v1->getKMWalkedDistance();
        return response()->json(['message'=> 'OK', 'gold_earned'=> $goldEarned, 'km_walked'=> $kmWalked]);
    }
}
