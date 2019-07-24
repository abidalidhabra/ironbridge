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

    /** widget item update **/
    public function widgetPriceUpdate(Request $request){
        $id = $request->get('id');
        $gold_price = $request->get('gold_price');
        WidgetItem::where('_id',$id)
                    ->update([
                        'gold_price'=>(float)$gold_price
                    ]);

        return response()->json([
                                'status'  => true,
                                'message' => 'Gold price updated successfully',
                                //'data'    => []
                            ]);          
    }

    /* color update */
    public function avatarColorUpdate(Request $request){
        $id     = $request->get('id');
        $status = $request->get('status');
        $index  = $request->get('index');
        $color_code = $request->get('color_code');
        
        /*$colorCodeCheck = substr($color_code, 0, 1);
        if ($colorCodeCheck != '#') {
            return response()->json([
                                'status'  => false,
                                'message' => 'Please color code # used',
                            ]);
        }*/

        if ($status == 'skin_color') {
            Avatar::where('_id',$id)->update(['skin_colors.'.$index => $color_code]);
        } elseif ($status == 'hairs_color') {
            Avatar::where('_id',$id)->update(['hairs_colors.'.$index => $color_code]);
        } elseif ($status == 'eyes_colors') {
            Avatar::where('_id',$id)->update(['eyes_colors.'.$index => $color_code]);   
        }

        return response()->json([
                                'status'  => true,
                                'message' => 'color code updated successfully',
                            ]);
    }
}
