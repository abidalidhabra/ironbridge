<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use Carbon\Carbon;
use Validator;
use Auth;
use App\Rules\IsPasswordValid;


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
			'dob' => "required|date_format:Y-m-d H:i:s",
		]);

		if ($validator->fails()) {
			return response()->json(['status'=>false,'message' => $validator->messages()->first()]);
		}

		$data = $request->all();
		$user->update($data);

		return response()->json(['status'=>true,'message' => 'Profile updated successfully.','data'=>$data]); 
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
            return response()->json(['status'=> false, 'message' => $validator->messages()->first()]);
        }

        $user_id  = Auth::user()->_id;
        $password = $request->get('password');

        User::where('_id',$user_id)->update(['password' => bcrypt($password)]);
    	return response()->json(['status'=>true,'message'=>'Your password has been updated successfully.']);
	}

	//UODATE SETTING
	public function updateSetting(Request $request){

		$validator = Validator::make($request->all(),[
						'setting'=>'required|in:music,sound',
					]);

		if ($validator->fails()) {
			return response()->json(['status'=>false,'message' => $validator->messages()->first()]);
		}

		$user    = Auth::user();
		$setting = $request->get('setting');
		if ($setting == 'music') {
			$user->settings = [
								'sound_fx' => false,
								'music_fx' => true
							];

		} else {
			$user->settings = [
								'sound_fx' => true,
								'music_fx' => false
							];
		}
		
		$user->save();

		return response()->json(['status'=>true,'message' => 'Profile setting successfully updated.']);
	}
}
