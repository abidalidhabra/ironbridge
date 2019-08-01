<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\PracticeGameFinishRequest;
use App\Models\v1\Game;
use App\Models\v1\User;
use App\Repositories\MiniGameRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;

class MGController extends Controller
{
    
    protected $user;
    
    public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function getGamesData(Request $request)
    {
    	try {
    		
            $response = [];
            $userMiniGames = auth()->user()->practice_games()->select('_id', 'game_id', 'piece', 'key', 'completed_at', 'piece_collected')->get();
            $userMiniGames->map(function($miniGame, $index) use (&$response){
                $response[] = $miniGame;
                $game = $miniGame->game()->first();
                $gamePracticeTargets = $game->practice_games_targets()->first();
                $response[$index]['game_identifier'] = $game->identifier;
                $response[$index]['target'] = $gamePracticeTargets->target;
                if ($game->identifier == 'jigsaw' || $game->identifier == 'sliding') {
                    $response[$index]['variation_image'] = collect($gamePracticeTargets->variation_image)->shuffle()->first();
                }
            });
            return response()->json(['message'=> 'Target info of each game retrieved successfully.', 'data'=> $response]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }

    // public function addSkeletonKey(Request $request, UserRepository $userRepository)
    // {
    // 	try {
    		
    //         $availableSkeletonKeys = $userRepository->addSkeletonKeyInAccount(auth()->user(), 1);

	   //  	return response()->json(['message'=> 'Skeleton key has been added successfully to your account.', 'available_skeleton_keys'=> $availableSkeletonKeys]);
    // 	} catch (Exception $e) {
    // 		return response()->json(['message'=> $e->getMessage()], 500);
    // 	}
    // }

    public function setupMiniGamesForUser(Request $request)
    {
        try {

            $miniGameRepository = new MiniGameRepository($this->user);
            $result = $miniGameRepository->createIfnotExist();

            return response()->json(['message'=> 'Your Mini games setup completed successfully.', 'data'=> $result]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function markTheGameAsComplete(PracticeGameFinishRequest $request)
    {
        try {

            $miniGameRepository = new MiniGameRepository($this->user);
            $availableSkeletonKeys = $miniGameRepository->completeAMiniGame($request);

            return response()->json(['message'=> 'This mini game is marked as completed.', 'available_skeleton_keys'=> $availableSkeletonKeys]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }
}
