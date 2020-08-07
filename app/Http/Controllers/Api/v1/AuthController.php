<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use App\Repositories\AppStatisticRepository;
use App\Repositories\MiniGameRepository;
use App\Rules\CheckThePassword;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use stdClass;

class AuthController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['login','checkUsernameEmail']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
       try {

            $maintenanceMode = (new AppStatisticRepository)->first();
            if ($maintenanceMode->maintenance) {
                return response()->json([ 'message'=>'Sorry! app is under maintenance.' ],503);
            }

            if ($request->has('username')) {
                $request['username'] = strtolower($request->username);
            }
            $validator = Validator::make($request->all(),[
                            'username' => "required",
                            'email' => "required_without:username|exists:users,email",
                            //'password' => ['required', new IsPasswordValid],
                            'password' => ['required', new CheckThePassword($request->username)],
                            'device_type'=> 'required|in:ios,android',
                            'device_id'=> 'required',
                            'device_model'=> 'required',
                            'device_os'=> 'required'
                        ]);
            
            if ($validator->fails()) {
                return response()->json(['message'=>$validator->messages()], 422);
            }
            
            if ($request->has('email') && $request->has('username')) {
                return response()->json(['message'=> ['username'=> ['username will not come with email.'] ] ], 422);
            }

            if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                $credentials = ['email'=> $request->username, 'password'=> $request->password];
            }else {
                $credentials = $request->only('username', 'password');
            }
            
            if ($token = $this->guard()->attempt($credentials)) {
                
                $user = $this->guard()->user();
                $wantToSave = false;
                if ($request->firbase_android_id || $request->firbase_ios_id) {
                    $wantToSave = true;
                    $user->firebase_ids = [
                        'android_id' => ($request->firbase_android_id)?$request->firbase_android_id:$user->firebase_ids['android_id'],
                        'ios_id' => ($request->firbase_ios_id)?$request->firbase_ios_id:$user->firebase_ids['ios_id']
                    ];
                }

                if (
                    $request->filled('device_type') || 
                    $request->filled('device_type') || 
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

                if ($user->additional && isset($user->additional['token']) && !empty($user->additional['token'])) {
                    $this->invalidateTheToken($user->additional['token']);
                }
                $user->additional = ['token'=> $token];

                if ($user->first_login == true) {
                    $user->first_login = false;
                    $user->save();
                }
                $user->save();

                if (
                    ($request->device_type == 'android' && $request->serverAppInfo->app_versions['android'] > $request->app_version) ||
                    ($request->device_type == 'ios' && $request->serverAppInfo->app_versions['ios'] > $request->app_version)
                ) {
                    return response()->json(['code'=> 14, 'message' => 'Please update an application.'], 500);
                }

                if ($user->practice_games()->count() == 0) {
                    $miniGameRepository = new MiniGameRepository($user);
                    $miniGameRepository->createIfnotExist();
                }
                    
                // UserHelper::minigameTutorials($user);
                return response()->json([
                    'message'=>'You logged-in successfully.', 
                    'token' => $token, 
                    'data' => $user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar', 'tutorials'])
                ],200);
            }

            return response()->json([
                'message'=>'Sorry! wrong credentials provided.', 
                'token' => "", 
                'data' => new stdClass()
            ],500);
       } catch (Exception $e) {
           // return response()->json(['message'=> $e->getMessage(), 'token' => "", 'data' => new stdClass()],500);
           return response()->json(['message'=> $e->getMessage()],500);
       }
    }

    /*public function minigameTutorials($user){
        if (!$user->minigame_tutorial) {
            $game = Game::where('status',true)->get();
            $minigameTutorial = []; 

            foreach ($game as $key => $value) {
                $minigameTutorial[] = [
                                            'game_id'      => $value->id,
                                            'completed_at' => null,
                                        ];
            }

            $user->minigame_tutorial = $minigameTutorial;

           $user->save();
        }
    }*/

    public function checkUsernameEmail(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'email'      => "required|string|email|unique:users,email",
                        'username'   => "required|string|unique:users,username",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message'=>$validator->messages()]);
        }

        return response()->json(['status'=>true,'data'=>[]]);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ],200);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function invalidateTheToken($token)
    {
        try {
            JWTAuth::setToken($token)->invalidate();
            return ['message'=> 'Token invalidated successfully.'];
        } catch ( TokenExpiredException $exception ) {
            // throw new Exception('Token already expired.');
            return ['message'=> 'Token already expired.'];
        } catch ( TokenInvalidException $exception ) {
            // throw new Exception('Invalid token provided.');
            return ['message'=> 'Invalid token provided.'];
        } catch ( JWTException $exception ) {
            // throw new Exception('Token is missing.');
            return ['message'=> 'Token is missing.'];
        }
    }
}
