<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;
use App\Models\v1\User;
class UserController extends Controller
{
    public function index()
    {
    	return view('admin.user.userList');
    }

    //GET USER
    public function getUsers(Request $request)
    {	
    	$user = User::select('first_name','last_name','username', 'email', 'mobile_no', 'dob', 'created_at')->orderBy('created_at','DESC')->get();
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
        ->make(true);
    }
}
