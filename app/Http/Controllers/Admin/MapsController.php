<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\TreasureLocation;
use Validator;


class MapsController extends Controller
{
    public function index(Request $request)
    {
    	return view('admin.maps.mapsList');
    }

    public function getMaps(Request $request){
    	$city = TreasureLocation::select('latitude','longitude','place_name','city','province','country')
    						->get();
    	return DataTables::of($city)
        ->addIndexColumn()
        ->addColumn('map', function($city){
        	//https://maps.google.com/?q=<lat>,<lng>
            //return '<a href="https://maps.google.com/?q='.$city->latitude.','.$city->longitude.'" target="_blank"><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
            return '<a href="'.route('admin.boundary_map',$city->id).'" target="_blank"><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
        })
        ->rawColumns(['map'])
        ->make(true);
    	
    }


    //boundary map
    public function boundaryMap($id){
        $id = $id;
        $location = TreasureLocation::where('_id',$id)
                                ->with(['complexities'=>function($query){
                                    $query->whereHas('place_clues');
                                  }])
                                ->first();

        $complexityarr = [];
        foreach ($location->complexities as $key => $complexity) {
            $complexityarr[] = $complexity['complexity'];
        }

        return view('admin.maps.boundary',compact('location','complexityarr'));
    }

     //boundary map
    public function starComplexityMap($id,$complexity){
        $id = $id;
        $location = TreasureLocation::where('_id',$id)
                                ->with(['complexities'=>function($query) use ($complexity){
                                    $query->where('complexity',$complexity);
                                  }])
                                ->first();
        
        $complexitySuf = $this->addOrdinalNumberSuffix($complexity);
        return view('admin.maps.start_complexity',compact('location','complexity','complexitySuf','id'));
    }

    //boundary map
    public function storeStarComplexity(Request $request){
        $validator = Validator::make($request->all(),[
                        'place_id'   => 'required',
                        'complexity' => 'required',
                        'coordinates'=> 'required|json',
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $id = $request->get('place_id');
        $complexity = $request->get('complexity');
        $location = TreasureLocation::where('_id',$id)
                                ->first();

        $complexity = $location->complexities()->updateOrCreate(['place_id'=>$id,'complexity'=>$complexity],['place_id'=>$id,'complexity'=>$complexity]);
        $complexity->place_clues()->updateOrCreate(['place_star_id'=>$complexity->_id],['place_star_id'=>$complexity->_id,'coordinates'=>json_decode($request->get('coordinates'))]);

       return response()->json([
            'status' => true,
            'message'=>'Complexity clue has been created successfully',
        ]);
    }

    public function clearAllClues($id){
        $location = TreasureLocation::where('_id',$id)
                                ->first();
        foreach ($location->complexities() as $key => $complexity) {
            $complexity->place_clues()->delete();
        }
        $location->complexities()->delete();
        return response()->json([
            'status' => true,
            'message'=>'Complexity clue removed successfully',
        ]);
    }

    function addOrdinalNumberSuffix($num) {
    if (!in_array(($num % 100),array(11,12,13))){
      switch ($num % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $num.'st';
        case 2:  return $num.'nd';
        case 3:  return $num.'rd';
      }
    }
    return $num.'th';
  }


    //ADD LOCATION SHOW
    public function addLocation(Request $request){
        return view('admin.maps.add_location');
    }

    //STORE LOCATION
    public function storeLocation(Request $request){
        $validator = Validator::make($request->all(),[
                        'boundary_arr' => 'required',
                        'place_name'   => 'required',
                        'latitude'     => 'required',
                        'longitude'    => 'required',
                        'city'         => 'required',
                        'province'     => 'required',
                        'country'      => 'required',
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        /*print_r($boundary_box);
        exit();*/
        
        // $boundaryArr = ;
        $boundary = [];
        foreach (json_decode($request->get('boundary_arr'),true) as $key => $value) {
            if (isset($value)) {
                $boundary[] = '['.$value.']';
            }

        }
        $boundingbox = $request->get('boundary_box');         
        $boundingbox = str_replace("((","",$boundingbox);
        $boundingbox = str_replace("))","",$boundingbox);
        $boundingbox = str_replace("(","",$boundingbox);
        $boundingbox = str_replace(")","",$boundingbox);
        $boundingbox = str_replace(")","",$boundingbox);
        $boundingbox = explode(',',$boundingbox);
        $new_arr = array();
        if(count($boundingbox)>0) {
            foreach($boundingbox as $key=>$val) {
                $new_arr[] = '"'.trim($val).'"';
            }
        }
        $boundingbox = "[".implode(',',$new_arr)."]";
        $boundingbox = htmlentities($boundingbox);
        
        $data = $request->all();
        $data['boundary_arr'] = '['.implode(',', $boundary).']';
        $data['boundingbox'] = $boundingbox;
        
        $location = TreasureLocation::create($data);        
        return response()->json([
            'status' => true,
            'message'=>'Location has been created successfully',
            'id'     => $location->_id,
        ]);
    }
}
