<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v3\City;
use App\Models\v3\Event;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;
use Yajra\DataTables\DataTables;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.events.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.events.create', [
            'cities'=> City::all()
        ]);
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
            'name'=> 'required',
            'city_id'=> 'required|exists:cities,_id',
            'centeric_points'=> 'required|array|min:2',
            'centeric_points.*'=> 'numeric',
            'total_radius'=> 'required|integer',
            'least_radius'=> 'required|integer',
            'total_compasses'=> 'required|integer',
            'weekly_max_compasses'=> 'required|integer',
            'deductable_radius'=> 'required|integer',
            'start_time'=> 'required|date_format:m/d/Y h:i A',
            'end_time'=> 'required|date_format:m/d/Y h:i A',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $city = City::find($request->city_id);
        $startDate = $this->localTOUTC($request->start_time, $city->timezone);
        $endDate = $this->localTOUTC($request->end_time, $city->timezone);

        $event = Event::create([
            'name'=> $request->name,
            'city_id'=> $request->city_id,
            'centeric_points'=> [
                'lat'=> (float)$request->centeric_points[0],
                'lng'=> (float)$request->centeric_points[1],
            ],
            'total_radius'=> (int) $request->total_radius,
            'least_radius'=> (int) $request->least_radius,
            'total_compasses'=> (int) $request->total_compasses,
            'weekly_max_compasses'=> (int) $request->weekly_max_compasses,
            'deductable_radius'=> (int) $request->deductable_radius,
            'time'=> [
                'start'=> $startDate,
                'end'=> $endDate
            ]
        ]);

        return response()->json(['message'=> 'Event has been added successfully.']);
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
        return view('admin.events.edit', [
            'event'=> Event::find($id),
            'cities'=> City::all()
        ]);
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

        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'city_id'=> 'required|exists:cities,_id',
            'centeric_points'=> 'required|array|min:2',
            'centeric_points.*'=> 'numeric',
            'total_radius'=> 'required|integer',
            'least_radius'=> 'required|integer',
            'total_compasses'=> 'required|integer',
            'weekly_max_compasses'=> 'required|integer',
            'deductable_radius'=> 'required|integer',
            'start_time'=> 'required|date_format:m/d/Y h:i A',
            'end_time'=> 'required|date_format:m/d/Y h:i A',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $city = City::find($request->city_id);
        $startDate = $this->localTOUTC($request->start_time, $city->timezone);
        $endDate = $this->localTOUTC($request->end_time, $city->timezone);
        // $startDate = new DateTime($request->start_time, new DateTimeZone($city->timezone));
        // $startDate = $startDate->setTimezone(new DateTimeZone('UTC'));
        // $startDate = new UTCDateTime($startDate->format('U') * 1000);
        // $endDate = new DateTime($request->end_time, new DateTimeZone($city->timezone));
        // $endDate = $endDate->setTimezone(new DateTimeZone('UTC'));
        // $endDate = new UTCDateTime($endDate->format('U') * 1000);
        // dd($startDate->toDateTime()->format('Y-m-d h:i:s A'), $endDate->toDateTime()->format('Y-m-d h:i:s A'));
        Event::where('_id', $id)->update([
            'name'=> $request->name,
            'city_id'=> $request->city_id,
            'centeric_points'=> [
                'lat'=> (float)$request->centeric_points[0],
                'lng'=> (float)$request->centeric_points[1],
            ],
            'total_radius'=> (int) $request->total_radius,
            'least_radius'=> (int) $request->least_radius,
            'total_compasses'=> (int) $request->total_compasses,
            'weekly_max_compasses'=> (int) $request->weekly_max_compasses,
            'deductable_radius'=> (int) $request->deductable_radius,
            'time'=> [
                'start'=> $startDate,
                'end'=> $endDate
            ]
        ]);
        return response()->json(['message'=> 'Event has been updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::destroy($id);
        return response()->json(['message'=> 'Event has been deleted successfully.']);
    }

    public function list(Request $request)
    {
        
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $events = Event::select('name','city_id','centeric_points','total_radius','least_radius','weekly_max_compasses','deductable_radius','time');
        $admin = auth()->user();
        
        if($search != ''){
            $events->where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('city_id','like','%'.$search.'%')
                ->orWhere('total_radius','like','%'.$search.'%')
                ->orWhere('least_radius','like','%'.$search.'%')
                ->orWhere('weekly_max_compasses','like','%'.$search.'%')
                ->orWhere('deductable_radius','like','%'.$search.'%');
            });
        }

        $events = $events->with('city:_id,name')->orderBy('created_at','DESC')->skip($skip)->take($take)->get();

        $count = Event::count();
        if($search != ''){
            $count = Event::where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('city_id','like','%'.$search.'%')
                ->orWhere('total_radius','like','%'.$search.'%')
                ->orWhere('least_radius','like','%'.$search.'%')
                ->orWhere('weekly_max_compasses','like','%'.$search.'%')
                ->orWhere('deductable_radius','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($events)
        ->addIndexColumn()
        ->addColumn('city',function($event){
            return $event->city->name;
        })
        ->addColumn('starts_at',function($event){
            return $event->time['start'];
        })
        ->addColumn('ends_at',function($event){
            return $event->time['end'];
        })
        ->addColumn('action', function($query) use ($admin){
            $data = '';
            if($admin->hasPermissionTo('Edit Event')){
                $data .=  '<a href="'.route('admin.events.edit',$query->id).'" data-toggle="tooltip" data-placement="left" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }
            if($admin->hasPermissionTo('Delete Event')){
                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$query->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
                </a>';
            }
            
            return $data;
        })
        ->rawColumns(['action','city'])
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

    public function localTOUTC($datetime, $tz)
    {
        $date = new DateTime($datetime, new DateTimeZone($tz));
        $date = $date->setTimezone(new DateTimeZone('UTC'));
        $date = new UTCDateTime($date->format('U') * 1000);
        return $date;
    }
}
