<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Avatar;
use App\Models\v1\WidgetItem;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;

class AvatarController extends Controller
{
	/** avatars index page **/
    public function index()
    {
    	return view('admin.avatar.index');
    }

    /** avatas get list in data table **/
    public function getAvatarsList(Request $request){
    	$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $avatar = Avatar::select('name','gender');
        if($search != ''){
            $avatar->where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('gender','like','%'.$search.'%');
            });
        }
        $avatar = $avatar->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = Avatar::count();
        if($search != ''){
            $count = Avatar::where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('gender','like','%'.$search.'%');
            })->count();
        }

         return DataTables::of($avatar)
        ->addIndexColumn()
        ->addColumn('action', function($avatar){
            return '<a href="'.route('admin.avatarDetails',$avatar->id).'"><i class="fa fa-eye iconsetaddbox"></i></a>';
        })
        ->rawColumns(['action'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
        ->setFilteredRecords($count)
        ->skipPaging()
        ->make(true);
    }


    /** avatas details **/
    public function avatarDetails($id){
    	$avatar = Avatar::where('_id',$id)
                        ->with(['widget_item'=>function($query){
                            $query->orderBy('widget_name','desc')
                            ->select('_id','widget_name','item_name','gold_price','avatar_id');
                            return $query;
                        }])
                        ->first();

         
        $widgetItem = $avatar->widget_item->groupBy('widget_name');
        
    	return view('admin.avatar.avatarDetails',compact('avatar','widgetItem'));
    }
}
