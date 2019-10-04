<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Admin;
use App\Models\v1\AdminPasswordSetLink;
use App\Models\v1\News;
use App\Models\v1\User;
use App\Models\v2\Hunt;
use App\Models\v2\HuntUser;
use App\Models\v2\Event;
use App\Models\v2\EventsUser;
use Carbon\Carbon;
use Validator;
use Auth;
use Hash;
use App\Models\v2\PlanPurchase;


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
        $data['first_record_date'] = $user->first()->created_at;
        $data['last_record_date'] = $user->last()->created_at;

        /* huntuser */
        $huntUser = HuntUser::select('user_id','hunt_id','status')
                            ->whereHas('hunt')
                            ->whereHas('user')
                            ->get();
        $data['huntCompleted'] = $huntUser->where('status','completed')->groupBy('hunt_id')->count();
        $data['huntProgress'] = $huntUser->whereIn('status',['participated', 'paused', 'running'])->groupBy('hunt_id')->count();

        $userHuntId = $huntUser->pluck('hunt_id')->toArray();
        $userHuntIdValue = array_count_values($userHuntId);
        arsort($userHuntIdValue);
        $data['huntTop'] = [];
        foreach ($userHuntIdValue as $key => $value) {
            $hunt = Hunt::select('name')->where('_id',$key)->first();
            $data['huntTop'][$value] = $hunt->name;
        }


        /* EVENT */
        $events = Event::select('_id','status','name')
                        ->get();
        $eventsUser = EventsUser::count();
        
        $data['total_event'] = $events->count();
        $data['event_participations'] = $eventsUser;
        
        /* PAYMENT */
        $plans = PlanPurchase::get();
        $data['total_payment'] =  number_format($plans->sum('price'),2);


        return view('admin.admin-home',compact('data'));

    }

    public function signedUpDateFilter(Request $request){
        $date = explode('-', $request->get('date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');
        $twoDays = Carbon::now()->subDays(2);
        
        /* USER  */
        $user = User::whereBetween('created_at', [$startAt,$endAt])->get();
        
        $data['device_ios']     = $user->where('device_type','ios')->count();
        $data['device_android'] = $user->where('device_type','android')->count();
        $data['male']       = $user->where('gender','male')->count();
        $data['female']     = $user->where('gender','female')->count();
        $data['total_user'] = $user->count();
        
        /* END USER */

        /* HUNT USED */
        $huntUser = HuntUser::select('user_id','hunt_id','status','created_at')
                            ->whereHas('hunt')
                            ->whereHas('user',function($query) use ($startAt,$endAt){
                                $query->whereBetween('created_at', [$startAt,$endAt]);
                            })
                            ->get();
        $data['hunt_completed'] = $huntUser->where('status','completed')->groupBy('hunt_id')->count();
        $data['hunt_progress'] = $huntUser->whereIn('status',['participated', 'paused', 'running'])->groupBy('hunt_id')->count();

        $userHuntId = $huntUser->pluck('hunt_id')->toArray();
        $userHuntIdValue = array_count_values($userHuntId);
        arsort($userHuntIdValue);
        $data['huntTop'] = [];
        $i = 1;
        foreach ($userHuntIdValue as $key => $value) {
            $hunt = Hunt::select('name')->where('_id',$key)->first();
            $data['huntTop'][$value] = $hunt->name;
            if ($i == 5) {
                break;
            }
            $i++;
        }
        
        return response()->json([
            'status'  => true,
            'message' => 'Get data successfully',
            'data'    => $data
        ]);
    }

    public function setPassword($token){
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

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
