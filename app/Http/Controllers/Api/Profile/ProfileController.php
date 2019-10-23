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
        $gameStatistics = $user->practice_games()->where('completion_times','>',0)
                            ->with('game: _id,identifier,name')
                            ->with('highestScore')
                            ->select('id', 'user_id', 'completion_times', 'game_id')
                            ->get()
                            ->map(function($practiceUserGame) {
                                $practiceUserGame = $practiceUserGame->toArray();
                                $practiceUserGame['highest_score'] = $practiceUserGame['highest_score'][0]['score'] ?? 0;
                                return $practiceUserGame;
                            });
        return response()->json(['message'=> 'OK', 'gold_earned'=> $goldEarned, 'km_walked'=> $kmWalked, 'game_statistics'=> $gameStatistics]);
    }
}
