<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Validator;
use App\Rules\CheckThePassword;

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

            return response()->json([
                'message'=>'You logged-in successfully.', 
                'token' => $token, 
                'data' => $this->guard()->user()->makeHidden(['reffered_by','updated_at','created_at'])
            ],200);
        }

        return response()->json([
            'message'=>'Sorry! wrong credentials provided.', 
            'token' => "", 
            'data' => new stdClass()
        ],500);
    }


    public function checkUsernameEmail(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'email'      => "required|string|email|unique:users,email",
                        'username'   => "required|string|max:10||unique:users,username",
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

        return response()->json(['message' => 'Successfully logged out']);
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
