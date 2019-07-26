<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Admin;
use App\Models\v1\AdminPasswordSetLink;
use App\Models\v1\News;
use App\Models\v1\User;
use App\Models\v1\Hunt;
use Carbon\Carbon;
use Validator;
use Auth;
use Hash;

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

    public function setPassword($token){

        $data = AdminPasswordSetLink::where('token',$token)->first();
        if(!$data){

            return redirect()->route('admin.login')->with('message','Not any account exist. Please contact admin');
        }

        if(is_null($data['used_on'])){
            return view('admin.auth.adminPasswordSet',compact('data'));
        }
        return redirect()->route('admin.login')->with('message','This password set link already used. You can generate password by forgot password.');
    }

    public function savePassword(Request $request,$id){

        $validator = Validator::make($request->all(),[
            'password'  => 'required',
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $admin = Admin::find($id);
        if($admin){
            $admin->password = Hash::make($request->get('password'));
            $admin->save();
            $data = AdminPasswordSetLink::where('token',$request->get('tokenData'))->first();
            $data->used_on = Carbon::now();
            $data->save(); 
            return redirect()->route('admin.login');      
        }

        return response()->json([
            'status' => false,
            'message'=>'Invalid password generation link',
        ]);
    }
}
