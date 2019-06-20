<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\v1\Avatar;
use App\Models\v1\User;
use App\Models\v1\UserBalancesheet;
use App\Models\v1\TreasureLocation;
use App\Models\v1\CityInfo;
use Auth;
use Illuminate\Http\Request;
use Route;
use UserHelper;
use Validator;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;

class UserController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt-auth', ['except' => ['register','getParks','watercheck']]);
    }

    public function register(Request $request){
    	
    	/* Validate the incoming request */
        $request['email'] = strtolower($request['email']);
        $request['username'] = strtolower($request['username']);
        $validator = Validator::make($request->all(),[
                        'first_name' => "required|string|max:20",
                        'last_name'  => "required|string|max:20",
                        'email'      => "required|string|email|unique:users,email",
                        'password' 	 => "required|string|min:6",
                        'username'   => "required|string|unique:users,username",
                        'dob'  		 => "required|date_format:d-m-Y",
                        'longitude' => 'required', 
                        'latitude'  => 'required',
                        'device_type'  => "nullable|string",
                        'firebase_id'  => "nullable|string",
                        //'reffered_by'  => "nullable|string|exists:users,reffered_id",
                        'reffered_by'  => "nullable|string",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

        \Log::info($request->all());
        $referralCode = $request->reffered_by;
        if (!empty($referralCode)) {
            $referralUser = User::where('reffered_id',$referralCode)->first();
        }

        $reffered_id = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 8)), 0, 8);

        // $data['dob'] = new MongoDBDate(Carbon::parse($dob));
        /* Get the parameters */
		$firsNname  = $request->first_name;
		$lastName 	= $request->last_name;
		$email 		= $request->email;
		$password 	= bcrypt($request->password);
		$username 	= $request->username;
		// $dob 		= $request->dob;
        $dob        = new MongoDBDate(Carbon::parse($request->get('dob')));
		$longitude 	= (float)$request->longitude;
		$latitude 	= (float)$request->latitude;
		$deviceType = $request->device_type;
		$firebaseId = $request->firebase_id;
		$refferedBy = $request->reffered_by;
        $goldBalance = 5000;

        /** Get the lcoation from coordinates **/
        $address = UserHelper::getUserLocation($latitude, $longitude);
        // print_r($request->all());
        // exit();
		/* Insert the data into the database */
		$user = User::create([
			'first_name' 	=> $firsNname,
			'last_name' 	=> $lastName,
			'email' 		=> $email,
			'password' 		=> $password,
			'username' 		=> $username,
			'dob' 			=> $dob,
            'address'       => $address,
            'gold_balance'  => $goldBalance,
			'location' => [
				'type' => 'Point',
				'coordinates' => [
					$longitude,
					$latitude,
				],
			],
			'device_type' 	=> ($request->filled('device_type'))?$deviceType:null,
			'firebase_ids' => [
				'android_id' => ($deviceType == 'android')?$firebaseId:null,
				'ios_id'	 => ($deviceType == 'ios')?$firebaseId:null,
			],
			'reffered_by' 	=> ($request->filled('reffered_by'))?$refferedBy:null,
            'reffered_id'   => $reffered_id,
            // 'settings'   => [
            //     'sound_fx' => true,
            //     'music_fx' => true,
            // ],
		]);

        /** Add balance sheet data for the gold balance **/
        TransactionHelper::makePassbookEntry($user->id,'SIGNUP','REWARD','CR',$goldBalance);
        // $user->balance_sheet()->save(new UserBalancesheet([
        //     'happens_at' => 'SIGNUP',
        //     'happens_because' => 'REWARD',
        //     'balance_type' => 'CR',
        //     'credit' => $goldBalance,
        // ]));

		/* return the response **/
        $credentials = $request->only('email', 'password');
		if ($token = Auth::guard('api')->attempt($credentials)) {
            
            
            // $response = UserHelper::playPractiveEvent($user,$token);
            // if ($response->status() != 200) {
            //     return response()->json([ 'message' => $response->getData()->message],500);
            // }

            // $user->delete();
            return response()->json([
            	'token' => $token,
            	'data'	=> $user,
            	'message' => 'Your registration has been completed successfully.',
            ],200);
        }else{
        	
        	return response()->json([
            	'token'	=> "",
            	'data'  => [],
            	'message' => 'Something went wrong while doing your registration.',
            ],500);
        }
    }

    public function getPayloadData(Request $request)
    {

    	$user = Auth::user();
    	$payloadData = UserHelper::getPerfixDetails($user);
    	return response()->json([
    		'data'	  => $payloadData,
    		'message' => 'Payload data has been retrieved successfully.',
    	],200);
    }

    public function setMyAvatar(Request $request)
    {

    	/* Validate the parameters */
    	$validator = Validator::make($request->all(),[
			'avatar_id'	  => "required|string|exists:avatars,_id",
			'eyes_color'  => "required|string",
			'hairs_color' => "required|string",
			'skin_color'  => "required|string",
    	]);

    	if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

    	$user = Auth::user();
    	$avatarId 	= $request->avatar_id;
		$eyeColor 	= $request->eyes_color;
		$hairColor  = $request->hairs_color;
		$skinColors = $request->skin_color;
        
        $primaryAvatar = Avatar::where('_id',$avatarId)->select('_id','gender')->first();
		
        $user->gender = $primaryAvatar->gender;
        $user->save();

        $user->avatar()
			->updateOrCreate(['user_id' => $user->id],[
				'avatar_id'  => $avatarId,
				'eyes_color'  => $eyeColor,
				'hairs_color' => $hairColor,
				'skin_color'=> $skinColors,
			]);

		return response()->json(['message' => 'Your avatar has been updated successfully.']);
    }

    public function checkMyBalance(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $data = UserBalancesheet::raw(function($collection) use ($userId){
            return $collection->aggregate([
                [
                    '$match' => [
                        'user_id' => $userId
                    ] 
                ],
                [
                    '$group' => [
                        '_id' => '$user_id',
                        'total_credit' => [ '$sum' => '$credit' ],
                        'total_debit' => [ '$sum' => '$debit' ],
                    ]
                ],
                 [
                    '$addFields' => [
                        'account_balance' => [ '$subtract' => ['$total_credit','$total_debit'] ],
                    ]
                ],
            ]);
        });

        return response()->json(['data'=>$data], 200);
    }

    public function getWarehouseData(Request $request)
    {

        $avatars = Avatar::all();
        $userAvatar =auth()->user()->avatar()->first();

        return response()->json([
            'avatars' => $avatars,
            'user_avatar' => $userAvatar
        ]);
    }

    public function getParks(Request $request)
    {
        return response()->json(CityInfo::select('latitude','longitude','place_name','place_id','boundary_arr','boundingbox')->get());
        //return response()->json(CityInfo::all());
    }

    public function watercheck($lat,$long){
        
        $GMAPStaticUrl = "https://maps.googleapis.com/maps/api/staticmap?center=".$lat.",".$long."&size=40x40&maptype=roadmap&sensor=false&zoom=40&key=AIzaSyBW0Gy-NfQI_Z8k9-X3M0MZoDgY-k_EdNg";  
        //echo $GMAPStaticUrl;
        $chuid = curl_init();
        curl_setopt($chuid, CURLOPT_URL, $GMAPStaticUrl);   
        curl_setopt($chuid, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($chuid, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = trim(curl_exec($chuid));
        curl_close($chuid);
        $image = imagecreatefromstring($data);

        // this is for debug to print the image
        ob_start();
        imagepng($image);
        $contents =  ob_get_contents();
        ob_end_clean();
        //echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";

        // here is the test : I only test 3 pixels ( enough to avoid rivers ... )
        $hexaColor = imagecolorat($image,0,0);
        $color_tran = imagecolorsforindex($image, $hexaColor);

        $hexaColor2 = imagecolorat($image,0,1);
        $color_tran2 = imagecolorsforindex($image, $hexaColor2);

        $hexaColor3 = imagecolorat($image,0,2);
        $color_tran3 = imagecolorsforindex($image, $hexaColor3);

        $red = $color_tran['red'] + $color_tran2['red'] + $color_tran3['red'];
        $green = $color_tran['green'] + $color_tran2['green'] + $color_tran3['green'];
        $blue = $color_tran['blue'] + $color_tran2['blue'] + $color_tran3['blue'];

        imagedestroy($image);
        //var_dump($red,$green,$blue);
        
        if($red == 510 && $green == 654 && $blue == 765)
            return json_encode(array("status"=>1));
        else
            return json_encode(array("status"=>0));
    }
}
