<?php

namespace App\Http\Controllers\Api\Profile;

use App\Exceptions\Profile\ChestBucketCapacityOverflowException;
use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\MarkTutorialAsCompleteRequest;
use App\Http\Requests\Profile\SubmitAnswerRequest;
use App\Http\Requests\User\AddTheChestRequest;
use App\Http\Requests\User\ChangeTheChestMGRequest;
use App\Http\Requests\User\OpenTheChestRequest;
use App\Http\Requests\User\SyncAnAccountRequest;
use App\Models\v2\MinigameHistory;
use App\Repositories\ComplexityTargetRepository;
use App\Repositories\Game\GameRepository;
use App\Repositories\HuntRewardDistributionHistoryRepository;
use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\GetRelicHuntParticipationRepository;
use App\Repositories\RelicRepository;
use App\Repositories\SeasonRepository;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\ChestService;
use App\Services\User\SyncAccount\SyncAccountFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{

    public function getRelics(Request $request)
    {
        $user = auth()->user();
        $goldEarned = (new HuntRewardDistributionHistoryRepository)->getModel()->where(['type'=> 'gold', 'user_id'=> $user->id])->sum('golds');
        $kmWalked = $user->hunt_user_v1->getKMWalkedDistance();
        // $gameStatistics = $user->practice_games()->where('completion_times','>',0)
        //                     ->with('game: _id,identifier,name')
        //                     ->with('highestScore')
        //                     ->select('id', 'user_id', 'completion_times', 'game_id')
        //                     ->get()
        //                     ->map(function($practiceUserGame) {
        //                         $practiceUserGame = $practiceUserGame->toArray();
        //                         $practiceUserGame['highest_score'] = $practiceUserGame['highest_score'][0]['score'] ?? 0;
        //                         return $practiceUserGame;
        //                     });

        $gameStatistics = (new UserHelper)->setUser($user)->getMinigamesStatistics();

        // $relics = (new RelicRepository)->getModel()
        //         ->active()
        //         ->select('_id', 'icon', 'complexity','game_id','game_variation_id')
        //         ->get()
        //         ->map(function($relic) use ($user) {
        //             $relic->acquired = $user->relics->where('id', $relic->_id)->first();
        //             return $relic; 
        //         });

        return response()->json([
            'message'=> 'OK', 
            'gold_earned'=> $goldEarned, 
            'km_walked'=> $kmWalked, 
            'game_statistics'=> $gameStatistics,
            // 'relics'=> $relics,
            'agent_status'=> $user->agent_status
        ]);
    }

    public function markTutorialAsComplete(MarkTutorialAsCompleteRequest $request)
    {
        $data = (new UserRepository(auth()->user()))->markTutorialAsComplete($request->module);
        return response()->json(['message' => 'Tutorial has been marked as complete.']); 
    }

    public function openTheChest(OpenTheChestRequest $request)
    {
        try {
            $user = auth()->user();

            if ($user->buckets['chests']['collected']) {
                $chestService = (new ChestService)
                                ->setUser($user)
                                ->open()
                                ->when(($request->skip == 'true'), function($class){
                                    $class->cutTheCharge('skipping_chest');
                                });
                return response()->json(['message' => 'Chest has been opened successfully.', 'data'=> $chestService->response()]); 
            }else{
                return response()->json(['message' => 'You don\'t have chest in your account to open.'], 422); 
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function changeTheChestMG(ChangeTheChestMGRequest $request)
    {
        $user = auth()->user();
        $huntStatistic = (new HuntStatisticRepository)->first(['id', 'chest']);
        if ($user->gold_balance >= $huntStatistic->chest['golds_to_skip_mg']) {

            $chestService = (new ChestService)->setUser($user);
            $chestService->changeChestMiniGame($request->minigames_ids);

            return response()->json([
                'message'=> 'mini-game has been changed.', 
                'data'=> [
                    'available_gold_balance'=> $chestService->getUser()->gold_balance,
                    'minigame'=> $chestService->getMiniGame()
                ]
            ]);
        }else{
            return response()->json([
                'message'=> 'you don\'t have enough golds to change the minigame.', 
            ], 422);
        }
    }

    public function removeTheChestFromBucket(Request $request)
    {
        $user = auth()->user();
        if ($user->buckets['chests']['collected']) {
            $chestService = (new ChestService)->setUser($user);
            $chestService->remove();

            return response()->json([
                'message'=> 'A chest has been removed from bucket.', 
                'chests_bucket'=> $user->buckets['chests'],
            ]);
        }else{
            return response()->json(['message'=> 'You don\'t have atleast single chest to remove.'], 422);
        }
    }

    public function addTheChest(AddTheChestRequest $request)
    {   
        try {
            $user = auth()->user();
            if ($user['buckets']['chests']['collected'] + 1 > $user['buckets']['chests']['capacity']) {
                throw new ChestBucketCapacityOverflowException("You don't have enough capacity to hold this chest");
            }else{
                $chestService = new ChestService;
                $chestService->setUser($user)->add($request->place_id);
                return response()->json([
                    'message'=> 'A chest has been added to bucket.', 
                    'chests_bucket'=> $chestService->getUser()->buckets['chests'],
                    'chest_freeze_data'=> (new ChestService)->setUser($user)->remainingFreezeTime()
                ]);
            }
        } catch (ChestBucketCapacityOverflowException $e) {
            return response()->json(['message'=> $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function setStreamingRelic(Request $request)
    {
        try {

            $validator = Validator::make($request->all(),[
                            'streaming_relic_id'=> "required|exists:relics,_id",
                            'skipped_relic_id'=> "required|exists:relics,_id",
                        ]);

            if ($validator->fails()) {
                return response()->json(['message'=> $validator->messages()->first()],422);
            }

            $user = auth()->user();
            $user->streaming_relic_id = $request->streaming_relic_id;
            $user->skipped_relic_id = $request->skipped_relic_id;
            $user->save();

            return response()->json([
                'message'=> 'Streaming relic has been added to your account.',
                'streaming_relic'=> (new UserRepository($user))->streamingRelic()
            ]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function temporelicAPI(Request $request)
    {
        (new UserRepository)->getModel()->chunk(200, function($users){
            foreach ($users as $user){
                $relic = $user->relics->last();
                if ($relic) {
                    $user->streaming_relic_id = $relic['id'];
                    $user->save();
                }
            }
        });

    }

    public function submitAnswer(SubmitAnswerRequest $request)
    {
        try {
            $data = (new UserRepository(auth()->user()))->submitAnswer($request->tag);
            return response()->json(['message' => 'Answer has been submitted successfully.']); 
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function syncAnAccountRequest(SyncAnAccountRequest $request)
    {
        try {

            $syncAccountFactory = new SyncAccountFactory;
            $emailAccountService = $syncAccountFactory->init($request->sync_to, auth()->user(), $request);
            return response()->json(['message' => 'Account has been successfully reset.', 'data'=> $emailAccountService->sync()]);
        }catch (ValidationException $e) {
            return response()->json(['message'=> collect($e->errors())->first()[0]], 422);
        }catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }
}
