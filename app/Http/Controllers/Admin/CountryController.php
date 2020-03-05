<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v3\Timezone;
use App\Models\v3\City;
use App\Models\v3\Country;
use Validator;
use Auth;
use Session;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;


class CountryController extends Controller
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
        return view('admin.country.index');
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
            'name'     => 'required|unique:countries,name',
            'currency_full_name' => 'required',
            'currency' => 'required',
            'currency_symbol' => 'required',
            'dialing_code'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        
        $data = $request->all();
        
        Country::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'News has been added successfully.',
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
            'code' => $request->get('code'),
            'currency'  => $request->get('currency'),
            'currency_full_name'     => $request->get('currency_full_name'),
            'currency_symbol' => $request->get('currency_symbol'),
            'dialing_code'  => $request->get('dialing_code'),
          //  '_id'     => $request->get('city_id'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required',
            'code' => 'required',
            'currency_full_name' => 'required',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
      
        $country = Country::where('_id',$request->get('country_id'))->first();

       

        $country->update($data);

        return response()->json([
            'status' => true,
            'message'=>'Country has been updated successfully.',
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
        Country::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'Country has been deleted successfully.',
        ]);
    }

    public function getCountryList(Request $request){
        $country = Country::get();
       // $admin = Auth::user();

        return DataTables::of($country)
        ->addIndexColumn()
        ->addColumn('action', function($country){
            $data = '';
                $data .=  '<a href="javascript:void(0)" class="edit_company" data-action="edit" data-id="'.$country->id.'" data-name="'.$country->name.'" data-code="'.$country->code.'" data-currency="'.$country->currency.'"  data-currency_full_name="'.$country->currency_full_name.'" data-currency_symbol="'.$country->currency_symbol.'" data-dialing_code="'.$country->dialing_code.'"   data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
           






                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$country->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
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
}