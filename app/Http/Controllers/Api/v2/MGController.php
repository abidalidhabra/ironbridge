<?php

namespace App\Http\Controllers\Api\v2;

use App\Exceptions\PracticeMiniGame\FreezeModeRunningException;
use App\Exceptions\PracticeMiniGame\PieceAlreadyCollectedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MiniGame\MarkMiniGameTutorialAsCompleteRequest;
use App\Http\Requests\MiniGame\UnlockAMiniGameRequest;
use App\Http\Requests\v2\PracticeGameFinishRequest;
use App\Models\v1\Game;
use App\Models\v1\User;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\MiniGameRepository;
use Exception;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;

class MGController extends Controller
{
    
    protected $user;
    protected $miniGameRepository;
    protected $userInterface;
    
    public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            $this->miniGameRepository = new MiniGameRepository($this->user);
            $this->userInterface = app(UserInterface::class)($this->user);
            return $next($request);
        });
    }

    public function getGamesData(Request $request)
    {
    	try {
    		
            $response = [];
            $userMiniGames = auth()->user()->practice_games()->select('_id', 'game_id', /*'piece', 'key',*/ 'completed_at', 'piece_collected', 'completion_times', 'unlocked_at')->get();
            $userMiniGames->map(function($miniGame, $index) use (&$response){
                $response[] = $miniGame;
                $response[$index]['freeze_mode'] = ($miniGame->completed_at && $miniGame->completed_at->gte(today()))?true:false;
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

            return response()->json([
                'message'=> 'This mini game is marked as completed.', 
                'available_skeleton_keys'=> $availableSkeletonKeys['available_skeleton_keys'],
                'completion_times'=> $availableSkeletonKeys['completion_times'],
                'pieces_collected'=> $this->user->pieces_collected,
            ]);
        } catch(PieceAlreadyCollectedException $e) {
            return response()->json(['message'=> $e->getMessage(), 'completion_times'=> $e->getcompletionTimes()], 422);
        } catch(FreezeModeRunningException $e) {
            return response()->json(['message'=> $e->getMessage(), 'completion_times'=> $e->getcompletionTimes()], 422);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage().' on line '.$e->getLine().' in '.$e->getFile()], 500);
        }
    }

    public function unlockAMiniGame(UnlockAMiniGameRequest $request)
    {

        // Deduct a skeleton key from user's account
        $availableSkeletonKeys = $this->userInterface->deductSkeletonKeys(1);
        
        // unlock a minigame
        $status = $this->miniGameRepository->unlockAMiniGame($request->game_id);
        
        // give response to client
        return response()->json([ 
            'message'=> 'Minigame unlocked successfully.', 
            'available_skeleton_keys'=> $availableSkeletonKeys, 
            // 'game_id'=> $request->game_id  
        ]);
    }

    public function markMiniGameTutorialAsComplete(MarkMiniGameTutorialAsCompleteRequest $request)
    {
        // mark the minigame tutorial's status as complete
        $availableSkeletonKeys = $this->userInterface->markMiniGameTutorialAsComplete($request->game_id);

        // give response to client
        return response()->json([ 
            'message'=> 'Minigame tutorial has been marked as complete.',
            // 'game_id'=> $request->game_id
        ]);
    }
}
