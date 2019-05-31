<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\News;
use App\Models\v1\Treasurelocation;

class AdminController extends Controller
{
    public function index()
    {
    	$data['news'] = News::count();
    	$data['treasure_locations'] = Treasurelocation::count();
    	
    	return view('admin.admin-home',compact('data'));

    }
}
