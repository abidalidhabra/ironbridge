<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use Carbon\Carbon;
use Validator;
use Auth;
use App\Rules\IsPasswordValid;
use MongoDB\BSON\UTCDateTime as MongoDBDate;



class ProfileController extends Controller
{
	//UPDATE PROFILE
	public function updateProfile(Request $request){
		
		$user    = Auth::user();
		
		$validator = Validator::make($request->all(),[
			'first_name' => "required|string|max:50",
			'last_name' => "required|string|max:50",
			'email' => "required|string|email|max:255|unique:users,email,".$user->_id.',_id',
			// 'email'                => "required|email|unique:users,email,{$user->id}",
			'dob' => "required|date_format:d-m-Y",
		]);

		if ($validator->fails()) {
			return response()->json(['message' => $validator->messages()],422);
		}

		$data = $request->all();
		$user->dob = new MongoDBDate(Carbon::parse($request->get('dob')));
		$user->first_name = $request->get('first_name');
		$user->last_name = $request->get('last_name');
		$user->email = $request->get('email');
		$user->save();

		return response()->json(['message' => 'Profile updated successfully.','data'=>$user]); 
	}


	//CHANGE PASSWORD
    public function changePassword(Request $request)
	{
		//validation stuff
        $validator = Validator::make($request->all(),[
                        'old_password'=>['required', new IsPasswordValid],
                        'password'=>'required',
                    ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()],422);
        }

        $user_id  = Auth::user()->_id;
        $password = $request->get('password');

        User::where('_id',$user_id)->update(['password' => bcrypt($password)]);
    	return response()->json(['message'=>'Your password has been updated successfully.']);
	}

	//UODATE SETTING
	public function updateSetting(Request $request){

		$validator = Validator::make($request->all(),[
						//'music'=>'required|in:true,false',
						//'sound'=>'required|in:true,false',
						'music'=>'required|boolean',
						'sound'=>'required|boolean',
					]);

		if ($validator->fails()) {
			return response()->json(['message' => $validator->messages()],422);
		}

		$user    = Auth::user();
		$user->settings = [
								'sound_fx' => ($request->get('sound') == "1")?true:false,
								'music_fx' => ($request->get('music') == "1")?true:false
							];
		$user->save();

		return response()->json(['message' => 'Profile setting successfully updated.','data'=>$user->settings]);
	}
}
