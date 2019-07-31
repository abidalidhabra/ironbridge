<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;
use App\Models\v1\User;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Models\v1\WidgetItem;
use Validator;
use MongoDB\BSON\ObjectId as MongoDBId;

class UserController extends Controller
{
    public function index()
    {
    	return view('admin.user.userList');
    }

    //GET USER
    public function getUsers(Request $request)
    {	
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

    	$user = User::select('first_name','last_name','username', 'email', 'mobile_no', 'gold_balance','created_at','skeleton_keys');
        if($search != ''){
            $user->where(function($query) use ($search){
                $query->where('first_name','like','%'.$search.'%')
                ->orWhere('last_name','like','%'.$search.'%')
                ->orWhere('username','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('mobile_no','like','%'.$search.'%')
                ->orWhere('dob','like','%'.$search.'%')
                ->orWhere('created_at','like','%'.$search.'%');
            });
        }
        $user = $user->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = User::count();
        if($search != ''){
            $count = User::where(function($query) use ($search){
                $query->where('first_name','like','%'.$search.'%')
                ->orWhere('last_name','like','%'.$search.'%')
                ->orWhere('username','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('mobile_no','like','%'.$search.'%')
                ->orWhere('dob','like','%'.$search.'%')
                ->orWhere('created_at','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($user)
        ->addIndexColumn()
        ->addColumn('name', function($user){
            return '<a href="'.route('admin.accountInfo',$user->id).'">'.$user->first_name.' '.$user->last_name.'</a>';
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->editColumn('skeleton_keys', function($user){
            $skeleton_keys = collect($user->skeleton_keys)->where('used_at',null)->count();

            return $skeleton_keys;
        })
        
        ->addColumn('action', function($user){
            return '<a href="'.route('admin.accountInfo',$user->id).'"><i class="fa fa-eye iconsetaddbox"></i></a>';
        })
        ->rawColumns(['name','action'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
        ->setFilteredRecords($count)
        ->skipPaging()
        ->make(true);
    }


    //USER Participated index 
    public function usersParticipatedList(Request $request){
        return view('admin.user.usersParticipatedList');
    }

    public function getUsertParticipatedList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $huntMode = $request->get('hunt_mode');
        
        if ($huntMode == 'challenge' || $huntMode == 'normal') {
            $huntUser = HuntUser::select('hunt_id','user_id','status','created_at','hunt_complexity_id','hunt_mode')
                            ->with([
                                    'Hunt:_id,name,fees',
                                    'user:_id,first_name,last_name',
                                    'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                    'hunt_complexities:_id,distance'
                                ])
                            ->where('hunt_mode' , $huntMode)
                            ->orderBy('created_at','DESC')
                            ->skip($skip)
                            ->take($take)
                            ->get();
            $count = HuntUser::where('hunt_mode' , $huntMode)
                            ->count();
        } else {
            $huntUser = HuntUser::select('hunt_id','user_id','status','created_at','hunt_complexity_id')
                        ->with([
                                'Hunt:_id,name,fees',
                                'user:_id,first_name,last_name',
                                'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                'hunt_complexities:_id,distance'
                            ])
                        ->orderBy('created_at','DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get();
            $count = HuntUser::count();
        }
        

        return DataTables::of($huntUser)
        ->addIndexColumn()
        ->addColumn('hunt_name', function($user){
            return $user->hunt->name;
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('username', function($user){
            return $user->user->first_name.' '.$user->user->last_name;
        })
        ->addColumn('fees', function($user){
            return $user->hunt->fees;
        })
        ->addColumn('clue_progress', function($user){
            $completedClue = $user->hunt_user_details()->where('status','completed')->count();
            $totalClue = $user->hunt_user_details()->count();
            
            return $completedClue.'/'.$totalClue;
        })
        ->addColumn('distance_progress', function($user){
                    $completedClues = 0;
                    $completedDist  = 0;
                    $totalClues = $user->hunt_user_details()->count();
                    $completedClues = $user->hunt_user_details()->where('status','completed')->count();
                    $totalDistance = $user->hunt_complexities->distance;
                    $completedDist = (($user->hunt_complexities->distance / $totalClues) * $completedClues);
                    
                    
                    return $completedDist.' / '.$totalDistance;
        })
        ->addColumn('view', function($user){
            return '<a href="'.route('admin.userHuntDetails',$user->id).'" >More</a>';
        })
        ->rawColumns(['view'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
        ->skipPaging()
        ->make(true);
    }

    //USER HUNT DETAILS
    public function userHuntDetails($id){
        $huntUserDetail = HuntUserDetail::select('hunt_user_id','game_id','game_variation_id','revealed_at','finished_in','status')      
                                        ->with('hunt_user:id,user_id')
                                        ->with(['game:_id,name','game_variation:_id,variation_name'])
                                        ->where('hunt_user_id',$id)
                                        ->get();
        $id = $huntUserDetail[0]->hunt_user->user_id;
        
        return view('admin.user.userHuntDetails',compact('huntUserDetail','id'));
    }



    //ACCOUNT INFO
    public function accountInfo($id){

        $user = User::with([
                            'hunt_user_v1:user_id,hunt_id,hunt_complexity_id,status,hunt_mode,complexity',
                            'hunt_user_v1.hunt:_id,fees',
                        ])
                    ->where('_id',$id)
                    ->first(); 
        
        $data['usedGold'] = 0;
        $data['currentGold'] = 0;
        $data['totalGold'] = 0;
        $data['skeleton'] = 0;
        
        $data['widgetsIdSelected'] = [];
        foreach ($user->widgets as $key => $value) {
            $data['widgetsIdSelected'][$value['id']] = ($value['selected'])?true:false;
        }

        $widgetsId =  collect($user->widgets)->pluck('id');
        
        $data['widget'] = WidgetItem::select('_id','widget_name','item_name','avatar_id','gold_price','widget_category')
                                ->whereIn('_id',$widgetsId)
                                ->orderBy('widget_name','desc')
                                ->get()
                                ->groupBy('widget_name');  
                                            
        if($user){
            $data['usedGold'] = $user->hunt_user_v1->where('hunt_mode','challenge')->pluck('hunt.fees')->sum();
            $data['currentGold'] = $user->gold_balance;
            $data['totalGold'] = $data['usedGold'] + $data['currentGold'];
            if($user->skeleton_keys){
                $userSkKeys = collect($user->skeleton_keys);
                $availSkKeys = $userSkKeys->where('used_at', null)->count();
                // $totalKey  = count(array_column($user->skeleton_keys,'used_at'));
                // $usedKey  = count(array_filter(array_column($user->skeleton_keys,'used_at')));
                // $data['skeleton'] = $totalKey-$usedKey;
                $data['skeleton'] = $availSkKeys;
            }
        }

        return view('admin.user.accountInfo',compact('id','user','data'));
    }

    //user treasureHunts
    public function treasureHunts($id){
        return view('admin.user.treasureHunts',compact('id'));
    }

    //LIST TREASUREHUNTS
    public function getTreasureHunts(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        
        $userId = $request->get('user_id');
        $status = $request->get('status');
        
        $status_value = ['participated', 'paused', 'running', 'completed'];
        if ($status == 'completed') {
            $status_value = ['completed'];
        } else if ($status == 'progress') {
            $status_value = ['participated', 'paused', 'running'];
        }

        $huntUser = HuntUser::select('hunt_id','user_id','status','created_at','hunt_complexity_id')
                            ->with([
                                'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                'hunt_complexities:_id,distance'
                            ])
                            ->where('user_id',$userId)
                            ->whereIn('status',$status_value)
                            ->orderBy('created_at','DESC')
                            //->skip($skip)
                            //->take($take)
                            ->get(); 

        
        $count = HuntUser::where('user_id',$userId)
                           ->whereIn('status',$status_value)
                           ->count();
        
        

        return DataTables::of($huntUser)
        ->addIndexColumn()
        ->addColumn('hunt_name', function($user){
            return $user->hunt->name;
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('status', function($user){
            return ucfirst($user->status);
        })
        ->addColumn('fees', function($user){
            return $user->hunt->fees;
        })
        ->addColumn('clue_progress', function($user){
            $completedClue = $user->hunt_user_details()->where('status','completed')->count();
            $totalClue = $user->hunt_user_details()->count();
            
            return $completedClue.'/'.$totalClue;
        })
        ->addColumn('distance_progress', function($user){
                    return 0;
                    $completedClues = 0;
                    $completedDist  = 0;
                    $totalClues = $user->hunt_user_details()->count();
                    $completedClues = $user->hunt_user_details()->where('status','completed')->count();
                    $totalDistance = $user->hunt_complexities->distance;
                    $completedDist = (($user->hunt_complexities->distance / $totalClues) * $completedClues);
                    
                    
                    return $completedDist.' / '.$totalDistance;
        })
        ->addColumn('view', function($user){
            return '<a href="'.route('admin.userHuntDetails',$user->id).'" >More</a>';
        })
        ->rawColumns(['view'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        //->setTotalRecords($count)
        //->skipPaging()
        ->make(true);
    }

    //activity
    public function activity($id){
        $user = User::select('gold_balance')
                    ->whereHas('hunt_user_v1')
                    ->with('hunt_user_v1:user_id,hunt_id,hunt_complexity_id,status,hunt_mode,complexity','hunt_user_v1.hunt:_id,fees')
                    ->where('_id',$id)
                    ->first(); 
        $data['usedGold'] = 0;
        $data['currentGold'] = 0;
        $data['totalGold'] = 0;

        if ($user) {
            $data['usedGold'] = $user->hunt_user_v1->where('hunt_mode','challenge')->pluck('hunt.fees')->sum();
            $data['currentGold'] = $user->gold_balance;
            $data['totalGold'] = $data['usedGold'] + $data['currentGold'];
        }

        return view('admin.user.activity',compact('id','data'));
    }

    //ADD GOLD
    public  function addGold(Request $request){
        $validator = Validator::make($request->all(), [
            'gold' => 'required|integer',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }        

        $id = $request->get('id');
        $gold = $request->get('gold');
        
        $user = User::where('_id',$id)->first();

        $user->gold_balance = (int)$gold + $user->gold_balance;
        $user->save();

        return response()->json([
            'status' => true,
            'message'=>'Gold has been added successfully.',
        ]);
    }

    //ADD Skeleton
    public function addSkeletonKey(Request $request){
        
        $validator = Validator::make($request->all(), [
            'skeleton_key' => 'required|integer',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        $addSkeletonNumber = $request->get('skeleton_key');

        $user = User::where('_id',$request->get('user_id'))->first();
        
        for ($i=0; $i < $addSkeletonNumber ; $i++) { 
            $skeletonKey = [
                'key'       => new MongoDBId(),
                'created_at'=> new MongoDBDate(),
                'used_at'   => null
            ];
            
            $user->push('skeleton_keys', $skeletonKey);
        }

        return response()->json([
                                'status'=>true,
                                'message'=> 'Skeleton keys has been added successfully',
                                //'available_skeleton_keys'=> $user->available_skeleton_keys
                            ]);
        
    }

}
