<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushToInactiveUsers;
use App\Models\v2\AppStatistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;

class AppSettingController extends Controller
{
    public function index()
    {
        return view('admin.app-settings', ['settings'=> AppStatistic::first()]);
    }

    public function update(Request $request)
    {
         $validator = Validator::make($request->all(),[
            'android_version'  => 'required',
            'maintenance_time'      => 'required',
            // 'end'      => 'required',
            'ios_version'      => 'required',
            'base_url'         => 'required|url',
            'inactivity_notification' => 'required|array',
            'inactivity_notification.when' => 'required|integer|min:1',
            'inactivity_notification.message' => 'required|string',
            'inactivity_notification.active' => 'required|boolean'
            // 'google_keys.web'    => 'required',
            // 'google_keys.android'=> 'required',
            // 'google_keys.ios'    => 'required'
        ],[
            'inactivity_notification.when.required'=> 'Inactivity notification duration is required.',
            'inactivity_notification.message.required'=> 'Inactivity notification message is required.'
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $message]);
        }

        $maintenanceTime = explode(' - ',$request->maintenance_time);
        $appStatistic = AppStatistic::first();
        $appStatistic->maintenance = filter_var($request->maintenance, FILTER_VALIDATE_BOOLEAN);
        $appStatistic->base_url = $request->base_url;
        $appStatistic->app_versions = ['android'=> $request->android_version, 'ios'=> $request->ios_version];
        $appStatistic->inactivity_notification = [
            'active'=> filter_var($request->inactivity_notification['active'], FILTER_VALIDATE_BOOLEAN),
            'when'=> (int)$request->inactivity_notification['when'],
            'message'=> $request->inactivity_notification['message']
        ];
        // $appStatistic->google_keys = [
        //     'web'=> $request->google_keys['web'], 
        //     'android'=> $request->google_keys['android'], 
        //     'ios'=> $request->google_keys['ios']
        // ];
        $appStatistic->maintenance_time = [
            'start'=> new UTCDateTime(Carbon::parse($maintenanceTime[0])),
            'end'=> new UTCDateTime(Carbon::parse($maintenanceTime[1]))
        ];
        $appStatistic->save();
        // dispatch(new SendPushToInactiveUsers);
        return response()->json(['status'=> true, 'message'=> 'Settings updated successfully.']);
    }
}
