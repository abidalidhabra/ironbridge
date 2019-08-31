<?php

namespace App\Repositories;

use App\Models\v1\Game;
use App\Models\v2\PracticeGameUser;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class MiniGameRepository
{
	
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function completeAMiniGame($request)
    {
        $mGUserData = PracticeGameUser::where('_id', $request->practice_game_user_id)->first();
        $mGUserData->completed_at = now();
        $mGUserData->piece_collected = true;
        $mGUserData->save();

        $result = $this->allotKeyIfEligible();
        return $result;
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
        $haveAllPieces = PracticeGameUser::where(['user_id'=> $userId, 'piece_collected'=> true])->get();

        /** Status of 1 & 2 & 3 Gateways **/
        if ($haveAllPieces->count() >= 3) {
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
            PracticeGameUser::whereIn('_id', $haveAllPieces->pluck('_id'))->update(['piece_collected'=> false]);
        }

        return $this->user->available_skeleton_keys;
    }

    public function miniGameParticipation()
    {
        return $this->user->practice_games()->first();
    }
}