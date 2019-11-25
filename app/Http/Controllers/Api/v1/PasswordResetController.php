<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\v1\User;
use App\Notifications\PasswordResetRequest;

class PasswordResetController extends Controller
{
    /** for email registered user **/
	public function forgotPassword(Request $request)
	{

		$request['email'] = strtolower($request->email);
		$validator = Validator::make($request->all(),[
			'email' => "required|email|exists:users,email",
		]);

		if ($validator->fails()) { 
			return response()->json(['message'=>$validator->messages()->first()],422);            
		}

		$user = User::where('email', $request->email)->first();

		$otp = rand(100000,999999);
		$user->otp = $otp;
		$user->save();
		$user->notify(new PasswordResetRequest($otp));

		return response()->json(['message' => 'Please check your email for a one time password.']);
	}

	public function matchOtp(Request $request)
	{   

		$validator = Validator::make($request->all(),[
			'email'     => 'required|email|exists:users,email',
			'otp'		=> 'required',
		]);
		if ($validator->fails()) { 
			return response()->json(['message'=>$validator->messages()->first()],422);            
		}

		$user = User::where('email', $request->get('email'))->where('otp', (int)$request->get('otp'))->first();
		if (!$user){
			return response()->json(['message' => 'This password reset otp is invalid.'],422);
		}

		return response()->json(['message' => 'one time password confirmed']);
	}

	public function resetpasswordByEmail(Request $request)
	{   
		\Log::info('---------------');
		\Log::info($request->all());
		$request['email'] = strtolower($request->email);
		$validator = Validator::make($request->all(),[
			'password'  => 'required',
			'email'     => 'required|email|exists:users,email',
		]);
		if ($validator->fails()) { 
			return response()->json(['message'=>$validator->messages()->first()],422);            
		}

		$user = User::where('email', $request->email)->first();
		
		$user->password = bcrypt($request->password);
		$user->otp 		= null;
		$user->save();

		return response()->json(['message' => 'Password Reset Successfully.']);
	}
}
