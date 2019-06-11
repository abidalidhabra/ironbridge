<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\TreasureLocation;
use App\Models\v1\PlaceStar;
use Validator;


class MapsController extends Controller
{
    public function index(Request $request)
    {
    	return view('admin.maps.mapsList');
    }

    public function getMaps(Request $request){
    	$city = TreasureLocation::select('latitude','longitude','place_name','city','province','country','custom_name')
    						->get();
    	return DataTables::of($city)
        ->addIndexColumn()
        ->editColumn('custom_name', function($city){
            if ($city->custom_name) {
                return $city->custom_name;
            } else {
                return '-';
            }
        })
        ->addColumn('map', function($city){
            return '<a href="'.route('admin.boundary_map',$city->id).'" ><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
        })
        ->addColumn('action', function($city){
            return '<a href="'.route('admin.edit_location',$city->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
                <a href="javascript:void(0)" class="delete_location" data-action="delete" data-placement="left" data-id="'.$city->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
            </a>';
        })
        ->rawColumns(['map','action'])
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
                                    $query->where('complexity',(int)$complexity);
                                  }])
                                ->first();
        $complexitySuf = $this->addOrdinalNumberSuffix($complexity);
        $complexityarr = PlaceStar::select('id','place_id','complexity')
                                ->where('place_id',$id)
                                ->pluck('complexity')
                                ->toArray();

        return view('admin.maps.start_complexity',compact('location','complexity','complexitySuf','id','complexityarr'));
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
        $complexity = (int)$request->get('complexity');
        $location = TreasureLocation::where('_id',$id)
                                ->first();

        $complexity = $location->complexities()->updateOrCreate(['place_id'=>$id,'complexity'=>$complexity],['place_id'=>$id,'complexity'=>(int)$complexity]);
        $complexity->place_clues()->updateOrCreate(['place_star_id'=>$complexity->_id],['place_star_id'=>$complexity->_id,'coordinates'=>json_decode($request->get('coordinates'))]);

       return response()->json([
            'status' => true,
            'message'=>'Clues has been added successfully',
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
            'message'=>'Clues has been removed successfully',
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

    //EDIT LOCATION
    public function editLocation($id){
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

        return view('admin.maps.edit_location',compact('location','complexityarr'));
    }

    //UPDATE LOCATION
    public function updateLocation(Request $request){
        $id = $request->get('id');
        $location = TreasureLocation::where('_id',$id)
                                    ->first();

        $boundaryArr = $request->get('boundary_arr');
        
        if($boundaryArr != ""){
            $boundary = [];
            foreach (json_decode($request->get('boundary_arr'),true) as $key => $value) {
                if (isset($value)) {
                    $boundary[] = '['.$value.']';
                }
            }
            $location->boundary_arr = '['.implode(',', $boundary).']';
        }

        $location->custom_name = $request->get('custom_name');
        $location->update();

        return response()->json([
            'status' => true,
            'message'=>'Location has been updated successfully',
        ]);
    }


    //STORE LOCATION
    public function storeLocation(Request $request){
        $validator = Validator::make($request->all(),[
                        'custom_name'  => 'required',
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

    //LOCATION DELETE
    public function locationDelete($id){
        //TreasureLocation::where('_id', $id)->delete();
        $location = TreasureLocation::where('_id',$id)->first();
        foreach ($location->complexities as $key => $complexity) {
            $complexity->place_clues()->delete();
        }
        $location->complexities()->delete();
        $location->delete();

        return response()->json([
            'status' => true,
            'message'=>'Location deleted successfully',
        ]);
    }

    //REMOVE STAR
    public function removeStar(Request $request){
        $id = $request->get('id');
        $complexity = $request->get('complexity');
        $placeStar = PlaceStar::where([
                            'place_id'   => $id,
                            'complexity' => (int)$complexity,
                        ])
                        ->first();
        
        $placeStar->place_clues()->delete();
        $placeStar->delete();
        return response()->json([
            'status' => true,
            'message'=>'Clue deleted successfully',
        ]);
    }
}
