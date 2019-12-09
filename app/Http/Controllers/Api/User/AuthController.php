<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\AppInMaintenanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Repositories\MiniGameRepository;
use App\Services\User\Authentication\LoginService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {

            // Create a user & Attempt to get token
            $registrationService = (new LoginService)
                                ->setRequest($request)
                                ->register()
                                ->generateAToken()
                                ->setFirebaseIds()
                                ->setAdditional()
                                ->save();

            if ($token = $registrationService->getToken()) {
                
                $user = $registrationService->getUser();

                if ($user->practice_games()->count() == 0) {
                    $miniGameRepository = (new MiniGameRepository($user))->createIfnotExist();
                }

                return response()->json([
                    'message'=>'You logged-in successfully.', 
                    'token' => $token, 
                    // 'data' => $user
                    'data' => $user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar', 'tutorials', 'additional'])
                ],200);
            }
            return response()->json(['message'=> ['password'=> ['Sorry! wrong credentials provided.'] ] ], 422);
       }catch (AppInMaintenanceException $e) {
           return response()->json(['message'=> $e->getMessage()], 503);
       }catch (Exception $e) {
           return response()->json(['message'=> $e->getMessage()], 500);
       }
    }
}
