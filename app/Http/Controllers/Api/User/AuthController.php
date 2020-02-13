<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\AppInMaintenanceException;
use App\Exceptions\AppNotUpdatedException;
use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Repositories\AppStatisticRepository;
use App\Services\User\Authentication\LoginService;
use App\Services\User\PostRegisterService;
use Exception;
use Illuminate\Http\Request;
use stdClass;

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
                                ->setDeviceInfo()
                                ->save()
                                ->throwIfAppNotUpdated();

            if ($token = $registrationService->getToken()) {
                
                $user = $registrationService->getUser();

                $postRegisterService = (new PostRegisterService)->setUser($user);
                $postRegisterService->configure();

                $defaultData = new stdClass();
                if($newRegistration = $registrationService->getNewRegistration()) {
                    $postRegisterService->configureForNewRegistration();
                    $apiResponse = $this->getPayloadData($request);
                    $defaultData = $apiResponse->original['data'];
                }

                return response()->json([
                    'message'=>'You logged-in successfully.', 
                    'token' => $token, 
                    'data' => $user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar', 'tutorials', 'additional', 'device_info', 'hat_selected']),
                    'default_data'  => $defaultData,
                    'new_registration'  => $newRegistration
                ],200);
            }
            return response()->json(['message'=> ['password'=> ['Sorry! wrong credentials provided.'] ] ], 422);
       }catch (AppInMaintenanceException $e) {
           return response()->json(['message'=> $e->getMessage()], 503);
       }catch (AppNotUpdatedException $e) {
           return response()->json(['code'=> $e->getExceptionCode(), 'message'=> $e->getMessage()], 500);
       }catch (Exception $e) {
           return response()->json(['message'=> $e->getMessage()], 500);
       }
    }

    public function getPayloadData($request)
    {
        $payloadData = UserHelper::getPerfixDetails(auth('api')->user());
        return response()->json(['data'=> $payloadData, 'message' => 'Payload data has been retrieved successfully.']);
    }

    public function getAppURL(Request $request)
    {
        if ($request->secret == 'ironbridge1779') {
            try {
                (new LoginService)->setRequest($request)->throwIfAppNotUpdated();
                $serverInfo = (new AppStatisticRepository)->first(['id', 'base_url']);
                return response()->json(['message' => 'OK.', 'url'=> $serverInfo->base_url], 500);
            } catch (AppInMaintenanceException $e) {
                return response()->json(['message'=> $e->getMessage()], 503);
            }catch (AppNotUpdatedException $e) {
                return response()->json(['code'=> $e->getExceptionCode(), 'message'=> $e->getMessage()], 500);
            }catch (Exception $e) {
                return response()->json(['message'=> $e->getMessage()], 500);
            }
        }else{
            return response()->json(['message' => 'you are not authorize to perform this action.'], 500);
        }
    }
}
