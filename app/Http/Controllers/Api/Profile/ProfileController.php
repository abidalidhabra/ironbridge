<?php

namespace App\Http\Controllers\Api\Profile;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\MarkTutorialAsCompleteRequest;
use App\Models\v2\MinigameHistory;
use App\Repositories\ComplexityTargetRepository;
use App\Repositories\Game\GameRepository;
use App\Repositories\HuntRewardDistributionHistoryRepository;
use App\Repositories\Hunt\GetRelicHuntParticipationRepository;
use App\Repositories\RelicRepository;
use App\Repositories\SeasonRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function getRelics(Request $request)
    {
        $user = auth()->user();
        $goldEarned = (new HuntRewardDistributionHistoryRepository)->getModel()->where(['type'=> 'gold', 'user_id'=> $user->id])->sum('golds');
        $kmWalked = $user->hunt_user_v1->getKMWalkedDistance();
        // $gameStatistics = $user->practice_games()->where('completion_times','>',0)
        //                     ->with('game: _id,identifier,name')
        //                     ->with('highestScore')
        //                     ->select('id', 'user_id', 'completion_times', 'game_id')
        //                     ->get()
        //                     ->map(function($practiceUserGame) {
        //                         $practiceUserGame = $practiceUserGame->toArray();
        //                         $practiceUserGame['highest_score'] = $practiceUserGame['highest_score'][0]['score'] ?? 0;
        //                         return $practiceUserGame;
        //                     });

        $gameStatistics = (new UserHelper)->setUser($user)->getMinigamesStatistics();

        $relics = (new RelicRepository)->getModel()
                ->active()
                ->with(['hunt_users'=> function($query) use ($user) {
                    $query->where('user_id', $user->id)->select('_id', 'status', 'relic_id', 'complexity');
                }])
                ->select('_id', 'icon', 'complexity','game_id','game_variation_id')
                ->get()
                ->map(function($relic) use ($user) {
                    $relic->acquired = $user->relics->where('id', $relic->_id)->first();
                    return $relic; 
                });

        return response()->json([
            'message'=> 'OK', 
            'gold_earned'=> $goldEarned, 
            'km_walked'=> $kmWalked, 
            'game_statistics'=> $gameStatistics,
            'relics'=> $relics,
            'agent_status'=> $user->agent_status
        ]);
    }

    public function markTutorialAsComplete(MarkTutorialAsCompleteRequest $request)
    {
        $data = (new UserRepository(auth()->user()))->markTutorialAsComplete($request->module);
        return response()->json(['message' => 'Tutorial has been marked as complete.']); 
    }
}
