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
    protected $request;

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

        $this->request = $request;
        if ($request->type == 'finished') {
            
            // log the action by overriding previous one
            (new MinigameEventFactory('practice', 'completed', $request->all()))->override();
        }else{

            // Throw an exception if cooldown period is active
            if ($this->practiceGameUser->completed_at && $this->practiceGameUser->completed_at->diffInHours() <= 24) {
            	
                $data = $this->allotKeyIfEligible($request, true);
                throw (new FreezeModeRunningException('This mini game is under the freeze mode.'))
                        ->setCompletionTimes($this->practiceGameUser->completion_times)
                        ->setAvailableSkeletonKeys($data['available_skeleton_keys'])
                        ->setLastPlay($this->practiceGameUser->last_play);
                        // ->setXPReward($data['xp_reward']);
            }else {
	            // Allot a key to user's account if aligible
	            $data = $this->allotKeyIfEligible($request);
            }
        }

        return [
        	'available_skeleton_keys'=> $data['available_skeleton_keys'] ?? $this->user->available_skeleton_keys, 
            'xp_reward'=> $data['xp_reward'] ?? new stdClass,
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
        /** check if bucket_size full **/
        $keyToBeCredit = (collect($this->user->skeleton_keys)->where('used_at', null)->count() >= $this->user->skeletons_bucket)?0:1;
        
        /** check if score passed **/
        $scores = $this->practiceGameUser->practice_games_targets->targets->sortBy('stage')->values();
        $countableTargets = $scores
                                ->where('score', '<=', (int)$request->score)
                                ->when($freezeMode, function($query) {
                                    return $query->where('stage', '>', $this->practiceGameUser->last_play['stage']);
                                })
                                ->values();
        $lastPlay = [];
        $youAreAtHigher = false;

        if ($countableTargets->count() > 0) {
            
            // if (!$freezeMode && $countableTargets->first()['stage'] == 1) {
            if ($this->request->increase_counter == 'true') {
                /** Increase completion_times only for stage 1 **/
                $this->addCompletionTimes($this->practiceGameUser);
                /** log the action **/
                (new MinigameEventFactory('practice', 'completed', $this->request->all()))->add();
                /** Mark the minigame as complete and piece as collected **/
                $this->addCompletedAt($this->practiceGameUser);
            }

            $lastPlay['stage'] = $countableTargets->last()['stage'];
            $lastPlay['score'] = (int)$request->score;
            $youAreAtHigher = true;
        }

        /** Status of 2 & 3 Gateways **/
        $xpReward = new stdClass;
        // if ($keyToBeCredit && $youAreAtHigher) {
        if ($youAreAtHigher) {
            
            // Add last play as proof
            $this->addLastPlay($lastPlay);
            // $xpReward = (new AddXPService)->setUser($this->user)->add($countableTarget['xp']);

            for ($i=0; $i < $countableTargets->count(); $i++) { 
                
                // increase the key piece & Add skeleton key to user's account if its 3rd piece
                $pieceToBeUpdate = (($this->user->pieces_collected + 1) == 3)? -2: 1; 
                $this->user->increment('pieces_collected', $pieceToBeUpdate);
                
                if ($keyToBeCredit && $pieceToBeUpdate < 0) {
                    (new UserRepository($this->user))->addSkeletonKeys($keyToBeCredit);
                }
            }
        }

        return [
            'xp_reward'=> (is_array($xpReward) && count($xpReward))? $xpReward: new stdClass,
            'available_skeleton_keys'=> $this->user->available_skeleton_keys
        ];
    }
}