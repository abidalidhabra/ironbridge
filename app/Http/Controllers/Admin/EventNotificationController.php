<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v3\City;
use App\Notifications\EventNotification;
use App\Repositories\User\UserRepository;
use App\Services\Event\EventNotificationService;
use App\v3\FCMNotificationsHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;
use Yajra\DataTables\DataTables;

class EventNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::whereHas('events', function($query){
            $query->where('time.start', '<=', new UTCDateTime(now()->addMonth()))->whereNull('started_at');
        })
        ->get();
        return view('admin.events.notifications', [
            'cities'=> $cities,
            'countries'=> collect()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'is_prescheduled'=> 'required|in:PRESCHEDULED,!PRESCHEDULED',
            'target'=> 'required|in:BYCOUNTRY,BYCITY',
            'target'=> 'required|in:BYCOUNTRY,BYCITY',
            'target_audience'=> 'required|in:LOCALS,!LOCALS',
            'cities'=> 'array|required_without:countries',
            'countries'=> 'array|required_without:cities',
            'title'=> 'required',
            'message'=> 'required',
            'send_at'=> 'required_if:is_prescheduled,PRESCHEDULED|date_format:m/d/Y h:i A',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        if ($request->is_prescheduled == 'PRESCHEDULED') {
            FCMNotificationsHistory::create([
                'title'=> $request->title,
                'message'=> $request->message,
                'target'=> $request->target,
                'target_audience'=> $request->target_audience,
                'cities'=> $request->cities,
                'countries'=> $request->countries,
                'send_at'=> new UTCDateTime(Carbon::parse($request->send_at))
            ]);
            $message = 'Your pre-scheduled notification has been added to the system.';
        }else{

            $eventNotificationService = (new EventNotificationService)->setNotification($request)->handle();
            
            if ($eventNotificationService->users) {
                $message = 'We have sent notification to '.$eventNotificationService->users->count().' users';
            }else{
                $message = 'No users there to receive notification';
            }
        }
        return response()->json(['message'=> $message, 'is_prescheduled'=> $request->is_prescheduled]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function list(Request $request)
    {
        
        $start = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $model = new FCMNotificationsHistory;

        $data = $model->when($search != '', function($query) use ($search) {
                        $query->where('title', 'like', '%'.$search.'%')
                              ->orWhere('message','like','%'.$search.'%')
                              ->orWhere('status','like','%'.$search.'%')
                              ->orWhere('send_at','like','%'.$search.'%');
                    })
                    ->skip($start)
                    ->take($take)
                    ->orderBy('send_at', 'desc')
                    ->get();

        $filterCount = $model->when($search != '', function($query) use ($search) {
                            $query->where('title', 'like', '%'.$search.'%')
                                ->orWhere('message','like','%'.$search.'%')
                                ->orWhere('status','like','%'.$search.'%')
                                ->orWhere('send_at','like','%'.$search.'%');
                        })
                        ->count();

        $totalCount = $model->count();
        
        return DataTables::of($data)
                ->addIndexColumn()
                 ->editColumn('send_at', function($notification) {
                    return $notification->send_at->format('d-m-Y h:i A'). ' UTC';
                })
                ->editColumn('status', function($notification) {
                    if ($notification['status'] == 'pending') {
                        return '<span class="badge badge-danger">Pending</span>';
                    }else{
                        return '<span class="badge badge-success">Sent</span>';
                    }
                })
                ->rawColumns(['status'])
                ->setTotalRecords($totalCount)
                ->setFilteredRecords($filterCount)
                ->skipPaging()
                ->make(true);
    }
}
