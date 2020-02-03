<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\AppStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
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
            'start'      => 'required',
            'end'      => 'required',
            'ios_version'      => 'required',
            'base_url'         => 'required|url',
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $message]);
        }
        
        $appStatistic = AppStatistic::first();
        $appStatistic->maintenance = filter_var($request->maintenance, FILTER_VALIDATE_BOOLEAN);
        $appStatistic->base_url = $request->base_url;
        $appStatistic->app_versions = ['android'=> $request->android_version, 'ios'=> $request->ios_version];
        $appStatistic->maintenance_time = [
            'start'=> new UTCDateTime(Carbon::parse($request->start)),
            'end'=> new UTCDateTime(Carbon::parse($request->end))
        ];
        $appStatistic->save();
        return response()->json(['status'=> true, 'message'=> 'Settings updated successfully.']);
    }
}
