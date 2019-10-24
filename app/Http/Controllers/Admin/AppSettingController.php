<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\AppStatistic;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function index()
    {
        return view('admin.app-settings', ['settings'=> AppStatistic::first()]);
    }

    public function update(Request $request)
    {
        $appStatistic = AppStatistic::first();
        $appStatistic->maintenance = filter_var($request->maintenance, FILTER_VALIDATE_BOOLEAN);
        // $appStatistic->maintenance = ($request->maintenance == 'true')? true: false;
        $appStatistic->save();
        return response()->json(['status'=> true, 'message'=> 'Settings updated successfully.']);
    }
}
