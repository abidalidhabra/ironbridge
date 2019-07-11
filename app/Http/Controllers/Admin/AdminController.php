<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\News;
use App\Models\v1\User;
use App\Models\v1\Hunt;

class AdminController extends Controller
{
    public function index()
    {
    	$data['news'] = News::count();
    	$treasureLocations = Hunt::select('city','province','country')->get();
    	$data['treasure_locations'] = $treasureLocations->count();
    	$data['total_city'] = $treasureLocations->groupBy('city')->count();
    	$data['total_province'] = $treasureLocations->groupBy('province')->count();
        $data['total_country'] = $treasureLocations->groupBy('country')->count();
    	
    	$user = User::get();
    	$data['device_ios']		= $user->where('device_type','ios')->count();
    	$data['device_android'] = $user->where('device_type','android')->count();
        $data['male']       = $user->where('gender','male')->count();
        $data['female']     = $user->where('gender','female')->count();
        $data['total_user'] = $user->count();
       
    	return view('admin.admin-home',compact('data'));

    }
}
