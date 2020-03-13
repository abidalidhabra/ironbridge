<?php

namespace App\Http\Controllers\Api\Hunt;

use App\Collections\GameCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hunt\CellDataRequest;
use App\Http\Requests\Hunt\ClaimPrizeForMinigameNodeRequest;
use App\Http\Requests\Hunt\HuntUserRequest;
use App\Http\Requests\Hunt\RevokeTheRevealRequest;
use App\Http\Requests\v1\ParticipateRequest;
use App\Models\v2\HuntStatistic;
use App\ReportedLocation;
use App\Repositories\AppStatisticRepository;
use App\Repositories\Hunt\ClaimTheBonusTreasurePrizeService;
use App\Repositories\Hunt\ClaimTheMinigameNodePrizeService;
use App\Repositories\Hunt\ClaimTheSkeletonNodePrizeService;
use App\Repositories\Hunt\Factory\HuntFactory;
use App\Repositories\Hunt\GetHuntParticipationDetailRepository;
use App\Repositories\Hunt\GetLastRunningRandomHuntRepository;
use App\Repositories\Hunt\GetRandomizeGamesService;
use App\Repositories\Hunt\GetRelicHuntParticipationRepository;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;
use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use App\Repositories\Hunt\TerminateTheLastRandomHuntRepository;
use App\Repositories\User\UserRepository;
use App\Services\Hunt\PaybleCellProviderService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Validator;
use stdClass;

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

    public function terminate(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'relic_hunt'  => 'required|in:true,false',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        (new TerminateTheLastRandomHuntRepository)
            ->terminate(
                ($request->relic_hunt == 'true')? true: false
            );
        return response()->json(['message' => 'Hunt is successfully terminated.']);
    }

    public function revokeTheReveal(RevokeTheRevealRequest $request)
    {
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);
        $huntUserDetail->revealed_at = null;
        $huntUserDetail->save();
        return response()->json(['message' => 'Hunt reveal is successfully revoked.']);
    }

    public function getMinigamesForNode(Request $request)
    {
        $minigames = (new GetRandomizeGamesService)->setUser(auth()->user())->get(10);
        return response()->json([
            'message' => 'minigame has been retrieved for the node.', 
            'minigames'=> $minigames->setUser(auth()->user())->loadTreasureNodesTargets()->addRemainingSeconds()
        ]);
    }

    public function claimPrizeForBonuseTreasureNode(Request $request)
    {
        // \DB::connection()->enableQueryLog();
        $reward = (new ClaimTheBonusTreasurePrizeService)->setUser(auth()->user())->do();
        // $queries = \DB::getQueryLog();
        // dd($queries);
        return response()->json(['message' => 'prize provided on the behalf of bonuse treasure node.', 'reward'=> $reward]);
    }    

    public function claimTheSkeletonNodePrizeService(Request $request)
    {
        $reward = (new ClaimTheSkeletonNodePrizeService)->setUser(auth()->user())->do();
        return response()->json(['message' => 'prize provided on the behalf of skeleton key node.', 'reward'=> $reward]);
    }

    public function claimPrizeForMinigameNode(ClaimPrizeForMinigameNodeRequest $request)
    {
        $reward = (new ClaimTheMinigameNodePrizeService)->setUser(auth()->user())->setGameId($request->game_id)->do();
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
            'power_status'=> [
                'power'=> $data['power'], 
                // 'till'=> (new UserRepository($user))->powerFreezeTill()
                // 'activated'=> $data['activated'] ?? false, 
            ]
        ]);
    }

    public function activateThePower(Request $request)
    {
        $user = auth()->user();
        if (isset($user->power_status['activated_at'])) {
            return response()->json([ 'message' => 'Power cannot be activate.' ], 422);
        }
        return response()->json([ 'message' => 'Power has been activated.', 'power_status'=> (new UserRepository($user))->activateThePower() ]);
    }

    public function getCellData(CellDataRequest $request)
    {
        try {
            $user = auth()->user();
        
            $paybleCellProviderService = new PaybleCellProviderService;
            
            // $response = [
            //     'cell_id'=> $paybleCellProviderService->getCellIDs($request, $user),
            //     'random_hunt_cell'=> $paybleCellProviderService->getRandomHuntsCells()
            // ];
            
            // if ($user->nodes_status && (isset($user->nodes_status['mg_challenge']) || isset($user->nodes_status['power']) || isset($user->nodes_status['bonus']))) {

            //     $playableRes = $paybleCellProviderService->getMinigamesCells(); 
            //     $playableNodes = collect($playableRes->locationsPerGameObjectType); 
                
            //     if (isset($user->nodes_status['power'])) {  
            //         $response['power_station_node'] = $playableNodes->first();  
            //     }   
                
            //     if (isset($user->nodes_status['mg_challenge'])) {   
            //         $response['minigame_node'] = $playableNodes->slice(1)->take(1)->first();    
            //     }
            //     if (isset($user->nodes_status['bonus'])) {    
                
            //         $response['bonus_nodes'] = $playableNodes->slice(2)->values();  
            //     }   
            // }

            $response = [];

            foreach (json_decode($request->coordinates, true) as $key => $coordinate) {

                $cell2ID = $paybleCellProviderService->getCellID(
                                array_merge($coordinate, ['level'=> $request->level])
                            );

                if (!$paybleCellProviderService->getCell2IDs()->contains('cell2ID', $cell2ID->cell2ID)) {

                    $responseToBeGiven = [];
                    $responseToBeGiven['cell2ID'] = $cell2ID->cell2ID;
                    $paybleCellProviderService->addCell2IDs($cell2ID);
                    $responseToBeGiven['random_hunt_cell'] = $paybleCellProviderService->getRandomHuntsCells();
                    
                    // if (
                    //     $user->nodes_status && 
                    //     (   
                    //         isset($user->nodes_status['mg_challenge']) || 
                    //         isset($user->nodes_status['power']) || 
                    //         isset($user->nodes_status['bonus'])
                    //     )
                    // ) {

                        $playableRes = $paybleCellProviderService->getMinigamesCells(); 
                        $playableNodes = collect($playableRes->locationsPerGameObjectType); 

                        // if (isset($user->nodes_status['power'])) {  
                            $responseToBeGiven['power_station_node'] = $playableNodes->first();  
                        // }   

                        // if (isset($user->nodes_status['mg_challenge'])) {   
                            $responseToBeGiven['minigame_node'] = $playableNodes->slice(1)->take(1)->first();    
                        // }

                        // if (isset($user->nodes_status['bonus'])) {    

                            $responseToBeGiven['bonus_nodes'] = $playableNodes->slice(2)->values();  
                        // }   
                    // }
                    $response[] = $responseToBeGiven;
                }
            }
            return response()->json(['message'=> 'Cell data has been retrieved.', 'data'=> $response]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }
    }

    public function reportTheLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'locationName'=> 'required',
                'reasons'   => 'required|array',
                'reasons.*' => 'required|in:BAD_LOCATION_REASON_UNSPECIFIED,OTHER,NOT_PEDESTRIAN_ACCESSIBLE,NOT_OPEN_TO_PUBLIC,PERMANENTLY_CLOSED,TEMPORARILY_INACCESSIBLE',
                'reasonDetails'  => 'required'
            ]);

            if ($validator->fails()){
                return response()->json(['message' => $validator->messages()->first()], 422);
            }

            auth()->user()->reported_locations()->create($request->all());
            $locations = ReportedLocation::notSended()->get();
            $huntStatistic = HuntStatistic::first(['_id', 'reported_loc_count']);

            if ($locations->count() >= $huntStatistic->reported_loc_count) {
                $paybleGoogleKey = (new AppStatisticRepository)->first(['_id', 'google_keys.web'])->google_keys['web'];
                
                $loc = $locations->map(function($data) {
                    $data->languageCode = "en-US";
                    unset($data->_id, $data->user_id, $data->sent_at, $data->created_at, $data->updated_at);
                    return $data;
                });

                $requestId = uniqid();
                $client = new Client();
                $apiResponse = $client->request('POST', 
                    "https://playablelocations.googleapis.com/v3:logPlayerReports?key=".$paybleGoogleKey, 
                    [
                        'json' => ["playerReports"=> $loc->toArray(), 'requestId'=> $requestId],
                        'http_errors'=> false
                    ]
                );

                $locations->each(function($location) use ($requestId){
                    $location->sent_at = now();
                    $location->requestId = $requestId;
                    $location->save();
                });

                $response = json_decode($apiResponse->getBody()->getContents());
            }
            return response()->json([
                'message'=> 'Location has been reported successfully.', 
                'google_response'=> $response ?? new stdClass(),
                'code'=> (isset($response))? $apiResponse->getStatusCode(): 0,
                'reason'=> (isset($response))? $apiResponse->getReasonPhrase(): "",
            ]);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()]);
        }
    }
}
