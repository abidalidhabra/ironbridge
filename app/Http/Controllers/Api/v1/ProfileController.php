<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use App\Rules\IsPasswordValid;
use App\Rules\User\UpdateHomeCity;
use App\Services\Event\EventService;
use App\Services\Event\EventUserService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Validator;



class ProfileController extends Controller
{
	//UPDATE PROFILE
	public function updateProfile(Request $request){
		
		$user    = Auth::user();
		$request['email'] = strtolower($request->get('email'));

		$validator = Validator::make($request->all(),[
			'first_name' => "string|max:50",
			'last_name' => "string|max:50",
			'email' => "string|email|max:255|unique:users,email,".$user->_id.',_id',
			// 'email'                => "required|email|unique:users,email,{$user->id}",
			'dob' => "required|date_format:dmY",
		]);

		if ($validator->fails()) {
			return response()->json(['message' => $validator->messages()],422);
		}

		 $data = $request->all();
		 $user->dob =Carbon::createFromFormat('dmY', $request->get('dob'))->format('d-m-Y');

		//  strtotime($request->get('dob'));
		 //$user->dob = Carbon::parse($request->get('dob'))->format('M d Y');
		if ($request->first_name) {
			$user->first_name = $request->get('first_name');
		}
		if ($request->last_name) {
			$user->last_name = $request->get('last_name');
		}
		if ($request->email) {
			$user->email = $request->get('email');
		}
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

	public function updateUserHomeCity(Request $request)
	{
		$user = auth()->user();
		
		$validator = Validator::make($request->all(),[
						'city_id'=>['required', 'exists:cities,_id', new UpdateHomeCity($user)],
					]);

		if ($validator->fails()) {
			return response()->json(['message' => $validator->messages()->first()],422);
		}

		$user->city_id = $request->city_id;
		$user->save();
		$response['message'] = 'Your home city has been updated successfully.';

        $event = (new EventUserService)->setUser($user)->running(['*'], true);
		if ($event) {
			$event->participations->first()->delete();
		}

		$event = (new EventService)->participateMeInEventIfAny($user, $request->city_id);
		if ($event) {
			$response['event_data'] = (new UserHelper)->prepareDateForEvent($user);
		}
		return response()->json($response);
	}
}
