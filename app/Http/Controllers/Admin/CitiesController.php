<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v3\Timezone;
use App\Models\v3\City;
use App\Models\v3\State;
use App\Models\v3\Country;
use Validator;
use Auth;
use Session;
use DateTimeZone;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;


class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $country = Country::get();
        $state = State::get();
        $tz = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        return view('admin.city.index',compact('country','state', 'tz'));
    }

    /**

'Canada/Eastern',
'Canada/Newfoundland',
'Canada/Pacific',
'Canada/Saskatchewan',
'Canada/Yukon');
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
            'name'     => 'required|unique:cities,name',
            'country_id' => 'required',
            'state_id' => 'required',
            'timezone'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        
        $data = $request->all();
         
        City::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'City has been added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
    public function update(Request $request,$id)
    {
        $data = [
            'name'     => $request->get('name'),
            'country_id' => $request->get('country_id'),
            'state_id'  => $request->get('state_id'),
            'timezone'  => $request->get('timezone'),
          //  '_id'     => $request->get('city_id'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required',
            'country_id' => 'required',
            'timezone' => 'required',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        //$data['valid_till'] = date('Y-m-d H:i:s',strtotime($request->get('valid_till')));
        $city = City::where('_id',$request->get('city_id'))->first();
        $city->update($data);
        return response()->json([
            'status' => true,
            'message'=>'City has been updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        City::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'City has been deleted successfully.',
        ]);
    }

    public function getCityList(Request $request){
        $city = City::select('country_id','state_id','name','timezone')->with('country')->with('state')->get();
       // $admin = Auth::user();

        return DataTables::of($city)
        ->addIndexColumn()
        ->addColumn('action', function($city){
            $data = '';
                $data .=  '<a href="javascript:void(0)" class="edit_company" data-action="edit" data-id="'.$city->id.'" data-cityname="'.$city->name.'" data-timezone="'.$city->timezone.'" data-country="'.$city->country_id.'"  data-state="'.$city->state_id.'"  data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
           
                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$city->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
                </a>';
           

            return $data;
        })
        ->order(function ($query) {
            if (request()->has('created_at')) {
                $query->orderBy('created_at', 'DESC');
            }

        })
        ->rawColumns(['action'])
        // ->setTotalRecords($count)
        // ->setFilteredRecords($count)
        ->skipPaging()
        ->make(true);
    }


    public function getTimezone(Request $request){
        $term = $request->q;
        $timezone = Timezone::select('timezone')->where('timezone','like','%'.$term.'%')->get(); 
     return response()->json([
            'status' => true,
            'timezone'=> $timezone,
        ]);
    }

    public function countryState(Request $request){
      $cid = $request->country_id;
     $state = State::where('country_id',$cid)->get(); 
     //return $state;
     return response()->json([
            'status' => true,
            'state'=> $state,
        ]);   
    }
}