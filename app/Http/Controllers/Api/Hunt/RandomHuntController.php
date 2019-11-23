<?php

namespace App\Http\Controllers\Api\Hunt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hunt\HuntUserRequest;
use App\Http\Requests\Hunt\RevokeTheRevealRequest;
use App\Http\Requests\v1\ParticipateRequest;
use App\Repositories\Hunt\Factory\HuntFactory;
use App\Repositories\Hunt\GetHuntParticipationDetailRepository;
use App\Repositories\Hunt\GetLastRunningRandomHuntRepository;
use App\Repositories\Hunt\GetRandomizeGameService;
use App\Repositories\Hunt\GetRelicHuntParticipationRepository;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;
use App\Repositories\Hunt\MinigameNodeClaimPrizeService;
use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use Exception;
use Illuminate\Http\Request;

class RandomHuntController extends Controller
{

    public function participate(ParticipateRequest $request)
    {
        $huntFactory = (new HuntFactory)->init($request);
        $response = $huntFactory->participate($request);
        $response['message'] = 'user has been successfully participated.';
        return response()->json($response);
    }

    public function initiateTheHunts(Request $request)
    {
        try {

            $data = (new GetLastRunningRandomHuntRepository)->get();
            return response()->json([
                'message' => 'Relic\'s information has been retrieved.', 
                'last_running_hunt'=> [
                    'hunt_user'=> $data['hunt_user'], 
                    'running_hunt_found'=> $data['running_hunt_found'], 
                    'remaining_clues'=> $data['remaining_clues'],
                    'total_remaining_clues'=> $data['total_remaining_clues'],
                    'total_completed_clues'=> $data['total_completed_clues'],
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

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

    public function terminate($hunt_user)
    {
        $hunt_user = (new HuntUserRepository)->find($hunt_user);
        if (!$hunt_user) {
            return response()->json(['message'=> 'You have provided invalid hunt user id provided.'], 500);
        }
        $hunt_user->status = 'terminated';
        $hunt_user->ended_at = now();
        $hunt_user->save();
        $hunt_user->hunt_user_details()->where('status', '!=', 'completed')->update(['status'=> 'terminated']);
        return response()->json(['message' => 'Hunt is successfully terminated.']);
    }

    public function revokeTheReveal(RevokeTheRevealRequest $request)
    {
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);
        $huntUserDetail->revealed_at = null;
        $huntUserDetail->save();
        return response()->json(['message' => 'Hunt reveal is successfully revoked.']);
    }

    public function getMinigameForNode(Request $request)
    {
        // \DB::connection()->enableQueryLog();
        $minigame = (new GetRandomizeGameService)->setUser(auth()->user())->get();
        // $queries = \DB::getQueryLog();
        // dd($queries);
        return response()->json(['message' => 'minigame has been retrieved for the node.', 'minigame'=> $minigame->load('treasure_nodes_target')]);
    }

    public function clainPrizeForMinigameNode(Request $request)
    {
        // \DB::connection()->enableQueryLog();
        $reward = (new MinigameNodeClaimPrizeService)->setUser(auth()->user())->do();
        // $queries = \DB::getQueryLog();
        // dd($queries);
        return response()->json(['message' => 'prize provided on the behalf of minigame.', 'reward'=> $reward]);
    }
}
