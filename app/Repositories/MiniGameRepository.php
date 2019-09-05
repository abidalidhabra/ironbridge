<?php

namespace App\Repositories;

use App\Exceptions\PracticeMiniGame\FreezeModeRunningException;
use App\Exceptions\PracticeMiniGame\PieceAlreadyCollectedException;
use App\Models\v1\Game;
use App\Models\v2\PracticeGameUser;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class MiniGameRepository
{
	
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     *
     * @param Illuminate\Http\Request
     * @return App\Exceptions\PracticeMiniGame\PieceAlreadyCollectedException
     * @return App\Exceptions\PracticeMiniGame\FreezeModeRunningException
     * @return Array
     *
     */
    public function completeAMiniGame($request)
    {
        
        // Findout the minigame data of user
        $practiceGameUser = PracticeGameUser::where('_id', $request->practice_game_user_id)->first();
        
        // Just Increase the completion time of minigame
        $this->addCompletionTimes($practiceGameUser);
        
        // Throw an exception if minigame's piece is already collected
        if ($practiceGameUser->piece_collected === true) {
            throw new PieceAlreadyCollectedException('This mini game is already completed, try different game.', $practiceGameUser->completion_times);
        }

        // Throw an exception if cooldown period is active
        // if ($practiceGameUser->completed_at && $practiceGameUser->completed_at->diffInHours() < 24) {
        if ($practiceGameUser->completed_at && $practiceGameUser->completed_at->gte(today())) {
            throw new FreezeModeRunningException('This mini game is under the freeze mode.', $practiceGameUser->completion_times);
        }

        // Mark the minigame as complete and peice as collected
        $this->markPracticeMiniGameAsComplete($practiceGameUser);

        // Allot a key to user's account if aligible
        $availableSkeletonKeys = $this->allotKeyIfEligible();

        return ['available_skeleton_keys'=> $availableSkeletonKeys, 'completion_times'=> $practiceGameUser->completion_times];
    }

    public function createIfnotExist()
    {
        if ($this->miniGameParticipation()) {
            throw new Exception("You already have mini game setup.");
        }else{
            
        	$data = [];
        	$games = Game::active()->get()->map(function($game, $index) use (&$data){
                
                // if ($index % 3 == 0) {
                //     $piece = 1;
                // }else if($index % 3 == 1){
                //     $piece = 2;
                // }else if($index % 3 == 2){
                //     $piece = 3;
                // }
                // $data[] = ['game_id'=> $game->id, 'piece'=> $piece];
                $data[$index]['game_id'] = $game->id;
                if ($game->practice_default_active) {
                    $data[$index]['unlocked_at'] = now();
                }
        	});
        	$practiceGameData = $this->user->practice_games()->createMany($data);
        	return $practiceGameData;
        }
    }

    public function allotKeyIfEligible()
    {
        /** Gateway 1 **/
        $keyToBeCredit = (collect($this->user->skeleton_keys)->where('used_at', null)->count() >= $this->user->skeletons_bucket)?0:1;
        
        /** Gateway 2 **/
        $userId = $this->user->id;
        // $haveAllPieces = PracticeGameUser::raw(function($collection) use ($userId) {
        //     return $collection->aggregate([
        //         [
        //             '$match' => [
        //                 'user_id' => ['$eq' => $userId],
        //                 'piece_collected' => ['$eq' => true]
        //             ]       
        //         ],  
        //         [   
        //             '$group' => [
        //                 '_id' => '$piece',
        //                 'completed_at' => ['$last' => '$completed_at'],
        //                 'piece_collected' => ['$last' => '$piece_collected'],
        //                 'user_id' => ['$last' => '$user_id'],
        //                 'key' => ['$last' => '$key'],
        //                 'id' => ['$last' => '$_id'],
        //             ]   
        //         ],  
        //         [   
        //             '$sort' => ['_id' => 1]   
        //         ]
        //     ]); 
        // });

        // $piecesInfo = PracticeGameUser::whereIn('_id', $haveAllPieces->pluck('id'))->get();
        // $haveAllPieces = PracticeGameUser::where(['user_id'=> $userId, 'piece_collected'=> true])->get();
        $peiceToBeUpdate = (($user->pieces_collected + 1) == 3)? 0: 1; 
        $user->pieces_collected = $peiceToBeUpdate;
        $user->save();

        /** Status of 1 & 2 & 3 Gateways **/
        // if ($haveAllPieces->count() >= 3) {
            if ($keyToBeCredit) {
                (new UserRepository($this->user))->addSkeletonKeys($keyToBeCredit);
                // $piecesInfo->markAsIncomplete();
            }
            // else{
            //     $planPurchaseData = $this->user->plans_purchases()->where('expandable_skeleton_keys', '>', 0)->first();
            //     if ($planPurchaseData && $planPurchaseData->expandable_skeleton_keys) {
            //         $planPurchaseData->expandable_skeleton_keys -= 1;
            //         $planPurchaseData->save();
            //         (new UserRepository($this->user))->addSkeletonKeys(1, ['plan_purchase_id' => $planPurchaseData->id]);
            //     }
            // }
            // PracticeGameUser::whereIn('_id', $haveAllPieces->pluck('_id'))->update(['piece_collected'=> false]);
        // }

        return $this->user->available_skeleton_keys;
    }

    public function miniGameParticipation()
    {
        return $this->user->practice_games()->first();
    }

    public function addCompletionTimes($practiceGameUser)
    {
        $practiceGameUser->completion_times += 1;
        $practiceGameUser->save();
        return $practiceGameUser;
    }

    public function markPracticeMiniGameAsComplete($practiceGameUser)
    {
        $practiceGameUser->completed_at = now();
        // $practiceGameUser->piece_collected = true;
        $practiceGameUser->save();
        return $practiceGameUser;
    }

    public function unlockAMiniGame($gameId)
    {
        return $this->user->practice_games()->where('game_id', $gameId)->update(['unlocked_at'=> new UTCDateTime(now())]);
    }
}