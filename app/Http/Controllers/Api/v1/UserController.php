<?php

namespace App\Http\Controllers\Api\v1;

use App\Factories\WidgetItemFactory;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\v1\Avatar;
use App\Models\v1\CityInfo;
use App\Models\v1\TreasureLocation;
use App\Models\v1\User;
use App\Models\v1\UserBalancesheet;
use App\Models\v1\WidgetItem;
use App\Repositories\MiniGameRepository;
// use App\Repositories\User\UserRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Route;
use UserHelper;
use Validator;
use stdClass;

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
    	try {
           

        /* Validate the incoming request */
        $request['email'] = strtolower($request['email']);
        $request['username'] = strtolower($request['username']);
        $validator = Validator::make($request->all(),[
                        'first_name' => "required|string|max:20",
                        'last_name'  => "required|string|max:20",
                        'email'      => "required|string|email|unique:users,email",
                        'password'   => "required|string|min:6",
                        'username'   => "required|string|unique:users,username",
                        'dob'        => "required|date_format:d-m-Y",
                        'longitude' => 'required', 
                        'latitude'  => 'required',
                        'device_type'  => "required|string",
                        'firebase_id'  => "nullable|string",
                        //'reffered_by'  => "nullable|string|exists:users,reffered_id",
                        'reffered_by'  => "nullable|string",
                        'device_id'=> 'required',
                        'device_model'=> 'required',
                        'device_os'=> 'required'
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
        $lastName   = $request->last_name;
        $email      = $request->email;
        // $password   = bcrypt($request->password);
        $password   = $request->password;
        $username   = $request->username;
        // $dob         = $request->dob;
        $dob        = new MongoDBDate(Carbon::parse($request->get('dob')));
        $longitude  = (float)$request->longitude;
        $latitude   = (float)$request->latitude;
        $deviceType = $request->device_type;
        $firebaseId = $request->firebase_id;
        $refferedBy = $request->reffered_by;
        $goldBalance = 500;

        /** Get the lcoation from coordinates **/
        // $address = UserHelper::getUserLocation($latitude, $longitude);
        // print_r($request->all());
        // exit();
        /* Insert the data into the database */
        $user = User::create([
            'first_name'    => $firsNname,
            'last_name'     => $lastName,
            'email'         => $email,
            'password'      => $password,
            'username'      => $username,
            'dob'           => $dob,
            'address'       => new stdClass(),
            'gold_balance'  => $goldBalance,
            'location' => [
                'type' => 'Point',
                'coordinates' => [
                    $longitude,
                    $latitude,
                ],
            ],
            'device_type'   => ($request->filled('device_type'))?$deviceType:null,
            'firebase_ids' => [
                'android_id' => ($deviceType == 'android')?$firebaseId:null,
                'ios_id'     => ($deviceType == 'ios')?$firebaseId:null,
            ],
            'reffered_by'   => ($request->filled('reffered_by'))?$refferedBy:null,
            'reffered_id'   => $reffered_id,
            'avatar'   => [
                "avatar_id" => "5c9b66739846f40e807a4498", 
                "eyes_color" => "#2a5aa1", 
                "hairs_color" => "#e5db96", 
                "skin_color" => "#f0cfb6"
            ],
            'widgets'   => [
                
                // ['id'=> "5d246f230b6d7b1a0a232486", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a23246e", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a23247a", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a232456", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a232462", 'selected'=> false],

                // ['id'=> "5d246f230b6d7b1a0a232484", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a23246d", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a232454", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a232461", 'selected'=> false],
                // ['id'=> "5d246f230b6d7b1a0a232478", 'selected'=> false],
                
                ['id'=> "5d246f230b6d7b1a0a232482", 'selected'=> true],
                ['id'=> "5d246f230b6d7b1a0a23245e", 'selected'=> true],
                ['id'=> "5d246f230b6d7b1a0a23246a", 'selected'=> true],
                ['id'=> "5d246f230b6d7b1a0a232453", 'selected'=> true],
                ['id'=> "5d246f230b6d7b1a0a232476", 'selected'=> true],
                ['id'=> "5d4424455c60e6147cf181b4", 'selected'=> true],

                // ['id'=> "5d246f0c0b6d7b19fb5ab594", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab57c", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab566", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab570", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab589", 'selected'=> false],

                // ['id'=> "5d246f0c0b6d7b19fb5ab593", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab56f", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab57b", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab565", 'selected'=> false],
                // ['id'=> "5d246f0c0b6d7b19fb5ab587", 'selected'=> false],
                
                ['id'=> "5d246f0c0b6d7b19fb5ab590", 'selected'=> true],
                ['id'=> "5d246f0c0b6d7b19fb5ab56d", 'selected'=> true],
                ['id'=> "5d246f0c0b6d7b19fb5ab562", 'selected'=> true],
                ['id'=> "5d246f0c0b6d7b19fb5ab578", 'selected'=> true],
                ['id'=> "5d246f0c0b6d7b19fb5ab584", 'selected'=> true],
                ['id'=> "5d4423d65c60e6147cf181a6", 'selected'=> true],
            ],
            'device_info'=> [
                'id'=> ($request->filled('device_id'))? $request->device_id: $user->device_info['id'],
                'type'=> ($request->filled('device_type'))? $request->device_type: $user->device_info['type'],
                'model'=> ($request->filled('device_model'))? $request->device_model: $user->device_info['model'],
                'os'=> ($request->filled('device_os'))? $request->device_os: $user->device_info['os'],
            ]
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
            
            $request->request->add(['user_id'=>$user->id]);
            $apiResponse = (new UserController)->getPayloadData($request);

            // $response = UserHelper::playPractiveEvent($user,$token);
            // if ($response->status() != 200) {
            //     return response()->json([ 'message' => $response->getData()->message],500);
            // }

            // $user->delete();
            if ($user->practice_games()->count() == 0) {
                $miniGameRepository = new MiniGameRepository($user);
                $miniGameRepository->createIfnotExist();
            }
            UserHelper::minigameTutorials($user);
            return response()->json([
                'token' => $token,
                'data'  => $user,
                'default_data'  => $apiResponse->original['data'],
                'message' => 'Your registration has been completed successfully.',
            ],200);
        }else{
            
            return response()->json([
                'token' => "",
                'data'  => [],
                'message' => 'Something went wrong while doing your registration.',
            ],500);
        } 
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],500);
        }
    }

    public function getPayloadData(Request $request)
    {

        if (!$request->has('user_id')) {
            $user = Auth::user();
        }else{
            $user = User::find($request->user_id);
        }

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
            'widgets'     => "required|array",
            'widgets.*' => "required|string|exists:widget_items,_id",
    	]);

    	if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

        // $usersAll = User::where('widgets.id','!=','5d246f0c0b6d7b19fb5ab590')->get();
        // $usersAll->map(function($user, $index){
        //     $user->push('widgets',['id'=>'5d246f0c0b6d7b19fb5ab590','selected'=>false]);
        //     $user->save();
        //     return $user;
        // });

    	$user       = Auth::user();
        $userId     = $user->id;
    	$avatarId 	= $request->avatar_id;
		$eyeColor 	= $request->eyes_color;
		$hairColor  = $request->hairs_color;
		$skinColors = $request->skin_color;
        $widgets    = $request->widgets;
        
        $primaryAvatar = Avatar::where('_id',$avatarId)->select('_id','gender')->first();
        $user->gender = $primaryAvatar->gender;
        $user->avatar = [
            'avatar_id' => $avatarId,
            'eyes_color' => $eyeColor,
            'hairs_color' => $hairColor,
            'skin_color' => $skinColors,
        ];
        $user->save();

        /** Extra ordinary setup **/
        // $newBee = User::where('_id', $userId)->first();
        /** Extra ordinary setup **/

        // User::where('_id',$user->id)
        //     ->where('widgets.selected', true)
        //     ->update(['widgets.$[].selected'=> false]);

        // if ($primaryAvatar->gender == 'female') {
        //     $newUser = User::where('_id', $userId)->select('_id', 'widgets')->get();
        //     $maleIdsGlobal = WidgetItem::whereHas('avatar', function($query) {
        //                         $query->where('gender', 'male');
        //                     })
        //                     ->get()
        //                     ->map(function($item) {
        //                         return $item->id;
        //                     });
        //     $maleIdsUser = collect($newUser->widgets)->filter(function($value, $key) use ($maleIdsGlobal){ 
        //                             return $maleIdsGlobal->contains($value->id);
        //                         });
        //     dump($maleIdsUser);
        //     dd("male", $maleIdsGlobal);
        //     // $maleItems = collect($newUser->widgets)->where()
        // }else {
        //     $newUser = User::where('_id', $userId)->select('_id', 'widgets')->first();
        //     $femaleIdsGlobal = WidgetItem::whereHas('avatar', function($query) {
        //                         $query->where('gender', 'male');
        //                     })
        //                     ->get()
        //                     ->map(function($item) {
        //                         return $item->id;
        //                     });
        //     $femaleIdsUser = collect($newUser->widgets)->filter(function($value, $key) use ($femaleIdsGlobal){ 
        //                             return $femaleIdsGlobal->contains($value['id']);
        //                         })->map(function($item) {
        //                             return $item['id'];
        //                         });
        //     dump($femaleIdsUser);
        //     dd("male", $femaleIdsGlobal);
        // }


        /*********************************************************************************************************/
        $newUser = User::where('_id', $userId)->select('_id', 'widgets')->first();

        $hairsProvided = false;
        foreach ($widgets as $index => $widget) {
            $hairsProvided = WidgetItem::where(['_id'=> $widget, 'widget_name'=> 'Hairs'])->count();
            if ($hairsProvided) {
                break;
            }
        }

        $globalIds = WidgetItem::whereHas('avatar', function($query) use ($primaryAvatar){
                                $query->where('gender', $primaryAvatar->gender);
                            })
                            ->get()
                            ->map(function($item) {
                                return $item->id;
                            });
        $userWidgetIds = collect($newUser->widgets)->filter(function($value, $key) use ($globalIds){ 
                            return $globalIds->contains($value['id']);
                        })->map(function($item) {
                            return $item['id'];
                        })->values()->toArray();

        User::where('_id',$user->id)
        ->update(['widgets.$[identifier].selected'=> false],[
            'arrayFilters'=> [ 
                [ "identifier.id"=> ['$in'=> $userWidgetIds] ] 
            ]
        ]);
        // dump($primaryAvatar->gender, $userWidgetIds);
        // dd($primaryAvatar->gender, $widgets);
        /*********************************************************************************************************/

        if (!$hairsProvided) {
            if ($user->gender == 'female') {
                $widgets[] = '5d4424455c60e6147cf181b4';
            }else{
                $widgets[] = '5d4423d65c60e6147cf181a6';
            }
        }
        User::where('_id',$user->id)
            ->update(['widgets.$[identifier].selected'=> true],[
                'arrayFilters'=> [ 
                    [ "identifier.id"=> ['$in'=> $widgets] ] 
                ]
            ]);
        
        $user = User::where('_id', $userId)->select('_id', 'avatar', 'widgets')->first();
		return response()->json([
            'message' => 'Your avatar has been updated successfully.', 
            'data'=> $user
        ]);
    }

    // public function checkMyBalance(Request $request)
    // {
    //     $user = Auth::user();
    //     $userId = $user->id;

    //     $data = UserBalancesheet::raw(function($collection) use ($userId){
    //         return $collection->aggregate([
    //             [
    //                 '$match' => [
    //                     'user_id' => $userId
    //                 ] 
    //             ],
    //             [
    //                 '$group' => [
    //                     '_id' => '$user_id',
    //                     'total_credit' => [ '$sum' => '$credit' ],
    //                     'total_debit' => [ '$sum' => '$debit' ],
    //                 ]
    //             ],
    //              [
    //                 '$addFields' => [
    //                     'account_balance' => [ '$subtract' => ['$total_credit','$total_debit'] ],
    //                 ]
    //             ],
    //         ]);
    //     });

    //     return response()->json(['data'=>$data], 200);
    // }

    // public function getWarehouseData(Request $request)
    // {

    //     $avatars = Avatar::all();
    //     $userAvatar =auth()->user()->avatar()->first();

    //     return response()->json([
    //         'avatars' => $avatars,
    //         'user_avatar' => $userAvatar
    //     ]);
    // }

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

    // public function minigameTutorialsCompleted(Request $request){
    //     $validator = Validator::make($request->all(),[
    //         'game_id'   => "required|string|exists:games,_id",
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message'=>$validator->messages()->first()], 422);
    //     }

    //     $user = Auth::User();
    //     $gameid = $request->get('game_id');
    //     $user = $user->where('minigame_tutorials.game_id',$gameid)
    //                 ->update(['minigame_tutorials.$.completed_at'=>new \MongoDB\BSON\UTCDateTime(new \DateTime('now'))]);
        
    //     return response()->json(['message' => 'Mini game updated successfully']); 
         
    // }

    // public function markTutorialAsUncomplete(Request $request)
    // {
    //     $data = (new UserRepository(auth()->user()))->markTutorialAsComplete($request->module);
    //     return response()->json(['message' => 'Tutorial has been marked as complete.']); 
    // }
}
