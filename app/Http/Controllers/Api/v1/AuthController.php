<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\MiniGameRepository;
use App\Rules\CheckThePassword;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // \App\Models\v2\HuntComplexity::where('created_at','<',today())
        // ->orderBy('created_at', 'desc')
        // ->get()
        // ->map(function($complexity, $index){
        //     // dd($complexity->created_at);
        //     $complexity->est_completion = $complexity->est_completion * 60;
        //     $complexity->save();
        //     return $complexity; 
        // });
        // exit;
       try {
           
            $request['username'] = strtolower($request->get('username'));
            $validator = Validator::make($request->all(),[
                            'username' => "required|exists:users,username",
                            //'password' => ['required', new IsPasswordValid],
                            'password' => ['required', new CheckThePassword($request->username)],
                        ]);
            
            if ($validator->fails()) {
                return response()->json(['message'=>$validator->messages()], 422);
            }

            $credentials = $request->only('username', 'password');

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

                if ($wantToSave) {
                    $user->save();
                }

                if ($user->practice_games()->count() == 0) {
                    $miniGameRepository = new MiniGameRepository($user);
                    $miniGameRepository->createIfnotExist();
                }

                return response()->json([
                    'message'=>'You logged-in successfully.', 
                    'token' => $token, 
                    'data' => $user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar'])
                ],200);
            }

            return response()->json([
                'message'=>'Sorry! wrong credentials provided.', 
                'token' => "", 
                'data' => new stdClass()
            ],500);
       } catch (Exception $e) {
           return response()->json([
                'message'=> $e->getMessage(), 
                'token' => "", 
                'data' => new stdClass()
            ],500);
       }
    }


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
}
