<?php

namespace App\Repositories;

use App\Models\v1\Game;
use App\Models\v2\PracticeGameUser;
use App\Repositories\UserRepository;
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
        $mGUserData->save();

        $result = $this->allotKeyIfEligible();
        return $result;
    }

    public function createIfnotExist()
    {
        if ($this->miniGameParticipation()) {
            throw new Exception("You already have mini game setup.");
        }

    	$data = [];
        $keyNumber = 0;
    	$games = Game::active()->get()->map(function($game, $index) use (&$data, &$keyNumber){
            
            if ($index % 3 == 0) {
                $keyNumber += 1;
                $piece = 1;
            }else if($index % 3 == 1){
                $piece = 2;
            }else if($index % 3 == 2){
                $piece = 3;
            }
            $data[] = ['game_id'=> $game->id, 'piece'=> $piece, 'key'=> $keyNumber];
    	});
    	$practiceGameData = $this->user->practice_games()->createMany($data);
    	return $practiceGameData;
    }

    public function allotKeyIfEligible()
    {
        /** Gateway 1 **/
        $keyToBeCredit = (collect($this->user->skeleton_keys)->where('used_at', null)->count() > 5)?0:1;
        
        /** Gateway 2 **/
        $userId = $this->user->id;
        $haveAllPieces = PracticeGameUser::raw(function($collection) use ($userId) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'user_id' => ['$eq' => $userId],
                        'completed_at' => ['$ne' => null]
                    ]       
                ],  
                [   
                    '$group' => [
                        '_id' => '$piece',
                        'completed_at' => ['$last' => '$completed_at'],
                        'user_id' => ['$last' => '$user_id'],
                        'key' => ['$last' => '$key'],
                        'id' => ['$last' => '$_id'],
                    ]   
                ],  
                [   
                    '$sort' => ['_id' => 1]   
                ]
            ]); 
        });

        $piecesInfo = PracticeGameUser::whereIn('_id', $haveAllPieces->pluck('id'))->get();

        /** Status of 1 & 2 Gateways **/
        if ($keyToBeCredit && $haveAllPieces->count() >= 3) {
            (new UserRepository($this->user))->addSkeletonKeyInAccount($keyToBeCredit);
            // $piecesInfo->markAsIncomplete();
            PracticeGameUser::whereIn('_id', $haveAllPieces->pluck('id'))->update(['completed_at'=> null]);
        }

        return $this->user->available_skeleton_keys;
    }

    public function miniGameParticipation()
    {
        return $this->user->practice_games()->first();
    }
}