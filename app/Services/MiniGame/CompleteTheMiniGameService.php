<?php

namespace App\Services\MiniGame;

use App\Exceptions\PracticeMiniGame\FreezeModeRunningException;
use App\Factories\MinigameEventFactory;
use App\Repositories\PracticeGameUserRepository;
use App\Repositories\User\UserRepository;
use App\Services\User\AddXPService;
use stdClass;

class CompleteTheMiniGameService
{

	protected $user;
	protected $practiceGameUser;

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
    public function complete($request)
    {
        
        // Findout the minigame data of user
        $this->practiceGameUser = (new PracticeGameUserRepository)->findOrFail($request->practice_game_user_id);
        $request->request->add(['game_id'=> $this->practiceGameUser->game_id]);

        if ($request->type == 'finished') {
            
            // log the action by overriding previous one
            (new MinigameEventFactory('practice', 'completed', $request->all()))->override();
        }else{
            
            // log the action
            (new MinigameEventFactory('practice', 'completed', $request->all()))->add();

            // Throw an exception if cooldown period is active
            if ($this->practiceGameUser->completed_at && $this->practiceGameUser->completed_at->diffInHours() <= 24) {
            	
                $data = $this->allotKeyIfEligible($request, true);
                throw (new FreezeModeRunningException('This mini game is under the freeze mode.'))
                        ->setCompletionTimes($this->practiceGameUser->completion_times)
                        ->setAvailableSkeletonKeys($data['available_skeleton_keys'])
                        ->setLastPlay($this->practiceGameUser->last_play)
                        ->setXPReward($data['xp_reward']);
            }else {
	            // Allot a key to user's account if aligible
	            $data = $this->allotKeyIfEligible($request);
            }
        }

        return [
        	'available_skeleton_keys'=> $data['available_skeleton_keys'] ?? $this->user->available_skeleton_keys, 
            'xp_reward'=> $data['xp_reward'],
        	'last_play'=> $this->practiceGameUser->last_play,
            'completion_times'=> $this->practiceGameUser->completion_times
        ];
    }

    public function addCompletionTimes()
    {
        $this->practiceGameUser->completion_times += 1;
        $this->practiceGameUser->save();
        return $this->practiceGameUser;
    }

    public function addCompletedAt()
    {
        $this->practiceGameUser->completed_at = now();
        $this->practiceGameUser->save();
        return $this->practiceGameUser;
    }

    public function addLastPlay($lastPlay)
    {
        $this->practiceGameUser->last_play = $lastPlay;
        $this->practiceGameUser->save();
        return $this->practiceGameUser;
    }

    public function allotKeyIfEligible($request, $freezeMode = false)
    {
        /** Gateway 1 **/
        $keyToBeCredit = (collect($this->user->skeleton_keys)->where('used_at', null)->count() >= $this->user->skeletons_bucket)?0:1;
        
        /** Gateway 2 **/
        $scores = $this->practiceGameUser->practice_games_targets->targets->sortBy('stage')->values();
        $minTarget = $scores->first();
        $countableTarget = $scores->when($freezeMode, function($query) {
                                return $query->where('stage', '>', $this->practiceGameUser->last_play['stage']);
                            })
                            ->first();

        // If users cross the target of stage 1:
        //  - Increase the completion time.
        //  - Mark the minigame as complete and piece as collected
        if (
            (isset($minTarget['score']) && ($minTarget['score'] <= (int)$request->score))
            // (isset($minTarget['time']) && ($minTarget['time'] >= (int)$request->time))
        ) {
            $this->addCompletionTimes($this->practiceGameUser);
            $this->addCompletedAt($this->practiceGameUser);
        }

        $lastPlay = [];
        $youAreAtHigher = false;
        $lastPlay['stage'] = $countableTarget['stage'] ?? 0;

        if (isset($countableTarget['score']) && ($countableTarget['score'] <= (int)$request->score)) {
            $lastPlay['score'] = (int)$request->score;
            $youAreAtHigher = true;
        }
        // else if(isset($countableTarget['time']) && ($countableTarget['time'] >= (int)$request->time)){
        //     $lastPlay['time'] = (int)$request->time;
        //     $youAreAtHigher = true;
        // }

        /** Status of 2 & 3 Gateways **/
        $xpReward = new stdClass;
        if ($keyToBeCredit && $youAreAtHigher) {
            
            // Add last play as proof
            $this->addLastPlay($lastPlay);
            $xpReward = (new AddXPService)->setUser($this->user)->add($countableTarget['xp']);

            // increase the key piece & Add skeleton key to user's account if its 3rd piece
            $pieceToBeUpdate = (($this->user->pieces_collected + 1) == 3)? -2: 1; 
            if ($lastPlay['stage'] == 1) {
                $this->user->increment('pieces_collected', $pieceToBeUpdate);
            }
            if ($pieceToBeUpdate < 0) {
                (new UserRepository($this->user))->addSkeletonKeys($keyToBeCredit);
            }
        }
        return [
            'xp_reward'=> (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass,
            'available_skeleton_keys'=> $this->user->available_skeleton_keys
        ];
    }
}