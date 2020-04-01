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
use Validator;
use App\Models\v1\User;

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

   public function guestUserregister(Request $request){
      try {
       if ($request->has('username')) {
                $request['username'] = strtolower($request->username);
            }
        $validator = Validator::make($request->all(),[
                            'type'=> 'in:google,facebook,apple,guest,emailupdate',
                            'google_id'=> 'required_if:type,google|unique:users,google_id',
                            'facebook_id'=> 'required_if:type,facebook|unique:users,facebook_id',
                            'apple_id'=> 'required_if:type,apple,emailupdate|unique:users,apple_id',
                            'email'=> 'required_unless:type,guest,apple|unique:users,email',
                            'apple_data'=> 'required_if:type,apple|json',
                           
                            'guestid' => "required|exists:users,_id",
                          
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }
         $user =User::find($request->guestid);
         if ($request->longitude || $request->latitude) {
                    $wantToSave = true;
                    $user->location = [
                        'type' => 'Point',
                        'coordinates' =>[
                    $request->longitude,
                    $request->latitude,
                ],
                    ];
                }
                $user->google_id = ($request->filled('google_id'))? $request->google_id: '';
                $user->facebook_id =($request->filled('facebook_id'))? $request->facebook_id: '';
                $user->apple_id =($request->filled('apple_id'))? $request->apple_id: '';
                $user->username =($request->filled('username'))? $request->username: '';
                $user->first_name =($request->filled('first_name'))? $request->first_name: '';
                $user->last_name =($request->filled('last_name'))? $request->last_name: '';
                $user->email =($request->filled('email'))? $request->email: '';
                $user->apple_data =($request->filled('apple_data'))? $request->apple_data: '';
                $user->last_login_as =$request->type; 
                if($request->filled('password')){
                $user->password = Hash::make($request->password);
                }
                if (
                    $request->filled('device_type') || 
                    $request->filled('device_id') || 
                    $request->filled('device_model') || 
                    $request->filled('device_os')
                ) {
                    $wantToSave = true;
                    $user->device_info = [ 
                        'id'=> ($request->filled('device_id'))? $request->device_id: $user->device_info['id'],
                        'type'=> ($request->filled('device_type'))? $request->device_type: $user->device_info['type'],
                        'model'=> ($request->filled('device_model'))? $request->device_model: $user->device_info['model'],
                        'os'=> ($request->filled('device_os'))? $request->device_os: $user->device_info['os'],
                    ];
                }

            $user->update();
            //if ($token = (new LoginService)->generateAToken()->getToken()) {
                
                //$user = $user->getUser();

                // $postRegisterService = (new PostRegisterService)->setUser($user);
                // $postRegisterService->configure();
                
                //$defaultData = new stdClass();
                //$newRegistration= new stdClass();

                return response()->json([
                    'message'=>'Your data updated successfully.', 
                    //'token' => $token, 
                    'data' => $user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar', 'tutorials', 'additional', 'device_info', 'hat_selected']),
                    
                ],200);
           

     
       }catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],500);
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
                $serverInfo = (new AppStatisticRepository)->first(['id', 'base_url', 'google_keys']);
                return response()->json(['message' => 'OK.', 'url'=> $serverInfo->base_url, 'google_keys'=> $serverInfo->google_keys], 500);
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
