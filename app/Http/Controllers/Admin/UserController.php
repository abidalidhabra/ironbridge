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
    	$user = User::select('first_name','last_name','username', 'email', 'mobile_no', 'dob', 'created_at')->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = User::count();
        return DataTables::of($user)
        ->addIndexColumn()
        ->addColumn('name', function($user){
            return $user->first_name.' '.$user->last_name;
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->editColumn('dob', function($user){
            return Carbon::parse($user->dob)->format('d-M-Y');
            // return $user->dob;
        })
        //->rawColumns(['created_at','photos','unlocked','profile_view','social_links','verified_detail','name','profile_photo'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
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
                                        ->where('hunt_user_id',$id)
                                        ->with(['game:_id,name','game_variation:_id,variation_name'])
                                        ->get();
        // echo "<pre>";
        // print_r($huntUserDetail->toArray());
        // exit();
        return view('admin.user.userHuntDetails',compact('huntUserDetail'));
    }

}
