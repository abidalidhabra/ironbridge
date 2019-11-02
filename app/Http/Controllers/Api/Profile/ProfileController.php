<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\MarkTutorialAsCompleteRequest;
use App\Repositories\HuntRewardDistributionHistoryRepository;
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

        
        $seasons = (new SeasonRepository)->getModel()
                    ->active()
                    ->with(['relics'=> function($query) use ($user) {
                        $query
                        ->active()
                        ->with(['participations'=> function($query) use ($user){
                            $query->where('user_id', $user->id)->select('_id', 'status', 'hunt_id', 'user_id');
                        }])
                        ->with(['rewards'=> function($query) use ($user){
                            $query->where('user_id', auth()->user()->id);
                        }])
                        ->select('id','name','desc','icon','season_id', 'user_id');
                    }])
                    ->get(['id', 'name', 'desc', 'icon'])
                    ->map(function($season) {
                        $season->relics->map(function($relic) {
                            $relic->occupied = false;
                            if (($relic->participations->count() && $relic->participations[0]->status == 'completed') || $relic->rewards->count()) {
                                $relic->occupied = true;
                            }
                            unset($relic->participations);
                            unset($relic->rewards);
                            return $relic;
                        });
                        return $season;
                    });

        return response()->json([
            'message'=> 'OK', 
            'gold_earned'=> $goldEarned, 
            'km_walked'=> $kmWalked, 
            'game_statistics'=> $gameStatistics,
            'seasons'=> $seasons
        ]);
    }

    public function markTutorialAsComplete(MarkTutorialAsCompleteRequest $request)
    {
        $data = (new UserRepository(auth()->user()))->markTutorialAsComplete($request->module);
        return response()->json(['message' => 'Tutorial has been marked as complete.']); 
    }
}
