<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v2\EventsUser;


class EventParticipatedController extends Controller
{
    //INDEX PAGE IN EVENT PARTICIPATED
    public function index(){
    	return view('admin.event.event_participated');
    }

	public function getEventParticipatedList(Request $request){
		$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $eventUser = EventsUser::select('user_id','event_id', 'completed_at' , 'attempts' ,'status','created_at');
        $admin = Auth::user();
        if($search != ''){
            $eventUser->where(function($query) use ($search){
                    $query->where('user_id','like','%'.$search.'%')
                    ->orWhere('event_id','like','%'.$search.'%')
                    ->orWhere('completed_at','like','%'.$search.'%')
                    ->orWhere('status','like','%'.$search.'%');
                });
        }
        $eventUser = $eventUser->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = EventsUser::count();
        if($search != ''){
            $count = EventsUser::where(function($query) use ($search){
                $query->where('user_id','like','%'.$search.'%')
                ->orWhere('event_id','like','%'.$search.'%')
                ->orWhere('completed_at','like','%'.$search.'%')
                ->orWhere('status','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($eventUser)
        ->addIndexColumn()
        ->addColumn('event_name', function($event){
            return ($event->event)?$event->event->name:'';
        })
        ->addColumn('user_name', function($event){
            return $event->user->first_name.' '.$event->user->last_name;
        })
        ->editColumn('completed_at', function($event){
        	return ($event->completed_at != null)?$event->completed_at->format('d-M-Y @ h:i A'):'-';
        })
        ->editColumn('status', function($event){
            if ($event->status == 'tobestart') {
                $status = 'Not Started';
            } else {
                $status = ucfirst($event->status);
            }
        	return $status;
        })
        ->addColumn('action', function($event) use ($admin){
            if($admin->hasPermissionTo('View Users')){
                return '<a href="'.route('admin.accountInfo',$event->user_id).'" data-toggle="tooltip" title="View" >View</a>';
            }
            return '';
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
}
