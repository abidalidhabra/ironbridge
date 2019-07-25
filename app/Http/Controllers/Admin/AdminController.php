<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\News;
use App\Models\v1\User;
use App\Models\v2\Hunt;
use App\Models\v2\HuntUser;

class AdminController extends Controller
{
    public function index()
    {
    	$data['news'] = News::count();
    	
        /* hunt */
        $treasureLocations = Hunt::select('city','province','country')->get();
    	$data['treasure_locations'] = $treasureLocations->count();
    	$data['total_city'] = $treasureLocations->groupBy('city')->count();
    	$data['total_province'] = $treasureLocations->groupBy('province')->count();
        $data['total_country'] = $treasureLocations->groupBy('country')->count();
        
        /* user */    	
    	$user = User::get();
    	$data['device_ios']		= $user->where('device_type','ios')->count();
    	$data['device_android'] = $user->where('device_type','android')->count();
        $data['male']       = $user->where('gender','male')->count();
        $data['female']     = $user->where('gender','female')->count();
        $data['total_user'] = $user->count();
       
        /* huntuser */
        $huntUser = HuntUser::select('user_id','hunt_id','status')
                            ->whereHas('hunt')
                            ->whereHas('user')
                            ->get();
        $data['huntCompleted'] = $huntUser->where('status','completed')->count();
        $data['huntProgress'] = $huntUser->whereIn('status',['participated', 'paused', 'running'])->count();
        
        return view('admin.admin-home',compact('data'));

    }
}
