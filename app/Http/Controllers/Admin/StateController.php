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
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;


class StateController extends Controller
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
        return view('admin.state.index',compact('country'));
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
            'name'     => 'required',
            'country_id' => 'required',
           
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        
        $data = $request->all();
       
        State::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'State has been added successfully.',
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
          
          //  '_id'     => $request->get('city_id'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required',
            'country_id' => 'required',
           
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $state = State::where('_id',$request->get('state_id'))->first();

       

        $state->update($data);

        return response()->json([
            'status' => true,
            'message'=>'State has been updated successfully.',
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
        State::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'State has been deleted successfully.',
        ]);
    }

    public function getStateList(Request $request){
        $State = State::select('country_id','name')->with('country')->get();
       // $admin = Auth::user();

        return DataTables::of($State)
        ->addIndexColumn()
        ->addColumn('action', function($State){
            $data = '';
                $data .=  '<a href="javascript:void(0)" class="edit_company" data-action="edit" data-id="'.$State->id.'" data-cityname="'.$State->name.'" data-country="'.$State->country_id.'"  data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
           
                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$State->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
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


  
}