<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\NewCityInfo;

class MapsController extends Controller
{
    public function index()
    {
    	return view('admin.maps.mapsList');
    }

    public function getMaps(){
    	$city = NewCityInfo::select('latitude','longitude','place_name','city','province')
    						->get();
    	return DataTables::of($city)
        ->addIndexColumn()
        ->addColumn('map', function($city){
        	//https://maps.google.com/?q=<lat>,<lng>
            return '<a href="https://maps.google.com/?q='.$city->latitude.','.$city->longitude.'" target="_blank"><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
        })
        ->rawColumns(['map'])
        ->make(true);
    	
    }
}
