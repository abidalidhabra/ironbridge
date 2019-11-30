<?php

namespace App\Http\Controllers\Api\Hunt;

use App\Collections\GameCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hunt\HuntUserRequest;
use App\Http\Requests\Hunt\RevokeTheRevealRequest;
use App\Http\Requests\v1\ParticipateRequest;
use App\Models\v2\HuntStatistic;
use App\Repositories\Hunt\ClaimTheBonusTreasurePrizeService;
use App\Repositories\Hunt\ClaimTheMinigameNodePrizeService;
use App\Repositories\Hunt\Factory\HuntFactory;
use App\Repositories\Hunt\GetHuntParticipationDetailRepository;
use App\Repositories\Hunt\GetLastRunningRandomHuntRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\Hunt\GetRelicHuntParticipationRepository;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;
use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Validator;

class RandomHuntController extends Controller
{

    public function participate(ParticipateRequest $request)
    {
        $huntFactory = (new HuntFactory)->init($request);
        $response = $huntFactory->participate($request);
        $response['message'] = 'user has been successfully participated.';
        return response()->json($response);
    }

    // public function initiateTheHunts(Request $request)
    // {
    //     try {

    //         $data = (new GetLastRunningRandomHuntRepository)->get();
    //         return response()->json([
    //             'message' => 'Relic\'s information has been retrieved.', 
    //             'last_running_hunt'=> [
    //                 'hunt_user'=> $data['hunt_user'], 
    //                 'running_hunt_found'=> $data['running_hunt_found'], 
    //                 'remaining_clues'=> $data['remaining_clues'],
    //                 'total_remaining_clues'=> $data['total_remaining_clues'],
    //                 'total_completed_clues'=> $data['total_completed_clues'],
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json(['message'=> $e->getMessage()], 500);
    //     }
    // }

    public function getRelicDetails(HuntUserRequest $request)
    {
        try {

            // $data = (new GetHuntParticipationDetailRepository)->get($request->hunt_user_id);
            $data = (new GetRelicHuntParticipationRepository)->get($request->hunt_user_id);
            return response()->json([
                'message' => 'Last Hunt\'s information has been retrieved.', 
                'relic_details'=> [
                    'hunt_user'=> $data['hunt_user'], 
                    'clues_data'=> $data['clues_data'],
                    'total_remaining_clues'=> $data['total_remaining_clues'],
                    'total_completed_clues'=> $data['total_completed_clues'],
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    // public function terminate($hunt_user)
    // {
    //     $hunt_user = (new HuntUserRepository)->find($hunt_user);
    //     if (!$hunt_user) {
    //         return response()->json(['message'=> 'You have provided invalid hunt user id provided.'], 500);
    //     }
    //     $hunt_user->status = 'terminated';
    //     $hunt_user->ended_at = now();
    //     $hunt_user->save();
    //     $hunt_user->hunt_user_details()->where('status', '!=', 'completed')->update(['status'=> 'terminated']);
    //     return response()->json(['message' => 'Hunt is successfully terminated.']);
    // }

    public function revokeTheReveal(RevokeTheRevealRequest $request)
    {
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);
        $huntUserDetail->revealed_at = null;
        $huntUserDetail->save();
        return response()->json(['message' => 'Hunt reveal is successfully revoked.']);
    }

    public function getMinigamesForNode(Request $request)
    {
        // \DB::connection()->enableQueryLog();
        $minigames = (new GetRandomizeGamesService)->setUser(auth()->user())->get(10);
        // $queries = \DB::getQueryLog();
        // dd($queries);
        return response()->json(['message' => 'minigame has been retrieved for the node.', 'minigames'=> $minigames->getTreasureNodesTargets()]);
    }

    public function claimPrizeForBonuseTreasureNode(Request $request)
    {
        // \DB::connection()->enableQueryLog();
        $reward = (new ClaimTheBonusTreasurePrizeService)->setUser(auth()->user())->do();
        // $queries = \DB::getQueryLog();
        // dd($queries);
        return response()->json(['message' => 'prize provided on the behalf of bonuse treasure node.', 'reward'=> $reward]);
    }

    public function claimPrizeForMinigameNode(Request $request)
    {
        $reward = (new ClaimTheMinigameNodePrizeService)->setUser(auth()->user())->do();
        return response()->json(['message' => 'prize provided on the behalf of minigame.', 'reward'=> $reward]);
    }

    public function updateARMode(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'status'=> "required|in:true,false",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()->first()], 422);
        }
        $user = auth()->user();
        $user->ar_mode = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
        $user->save();
        return response()->json(['message' => 'AR Mode has been updated.', 'ar_mode'=> $user->ar_mode]);
    }

    public function boostThePower(Request $request)
    {
        $user = auth()->user();
        // $huntStatistic = HuntStatistic::first(['_id', 'power_ratio']);
        $data = (new UserRepository($user))->addPower((int)$request->power);
        return response()->json([
            'message' => 'Power has been boosted.',
            'power_station'=> [
                'power'=> $data['power'], 
                // 'till'=> (new UserRepository($user))->powerFreezeTill()
                'activated'=> $data['activated'] ?? false, 
            ]
        ]);
    }

    public function activateThePower(Request $request)
    {
        $user = auth()->user();
        if (isset($user->power_status['activated_at'])) {
            return response()->json([ 'message' => 'Power cannot be activate.' ], 422);
        }
        return response()->json([ 'message' => 'Power has been activated.', 'power_station'=> (new UserRepository($user))->activateThePower() ]);
    }
}
