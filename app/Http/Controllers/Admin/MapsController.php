<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\TreasureLocation;
use App\Models\v2\Hunt;
use App\Models\v1\PlaceStar;
use App\Models\v2\HuntComplexity;
use Validator;
use Carbon\Carbon;
use App\Models\v1\Game;
use App\Models\v1\GameVariation;
use MongoDB\BSON\ObjectID;

class MapsController extends Controller
{
    public function index(Request $request)
    {
    	return view('admin.maps.mapsList');
    }

    public function getMaps(Request $request){
    	$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $city = Hunt::select('latitude','longitude','place_name','city','province','country','name','updated_at');
        if($search != ''){
            $city->where(function($query) use ($search){
                $query->orWhere('place_name','like','%'.$search.'%')
                ->orWhere('city','like','%'.$search.'%')
                ->orWhere('province','like','%'.$search.'%')
                ->orWhere('country','like','%'.$search.'%')
                ->orWhere('name','like','%'.$search.'%')
                ->orWhere('updated_at','like','%'.$search.'%');
            });
        }
        $city = $city->skip($skip)->take($take)->orderBy('updated_at', 'DESC')->get();
        $count = Hunt::count();
        if($search != ''){
            $count = Hunt::where(function($query) use ($search){
                $query->orWhere('place_name','like','%'.$search.'%')
                ->orWhere('city','like','%'.$search.'%')
                ->orWhere('province','like','%'.$search.'%')
                ->orWhere('country','like','%'.$search.'%')
                ->orWhere('name','like','%'.$search.'%')
                ->orWhere('updated_at','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($city)
        ->addIndexColumn()
        ->editColumn('name', function($city){
            if ($city->name) {
                return $city->name;
            } else {
                return '-';
            }
        })
        ->editColumn('updated_at', function($city){
            return Carbon::parse($city->updated_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('map', function($city){
            return '<a href="'.route('admin.boundary_map',$city->id).'" ><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
        })
        ->addColumn('action', function($city){
            return '<a href="'.route('admin.edit_location',$city->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
                <a href="javascript:void(0)" class="delete_location" data-action="delete" data-placement="left" data-id="'.$city->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
            </a>';
        })
        ->order(function ($city) {
            if (request()->has('updated_at')) {
                $city->orderBy('updated_at', 'DESC');
            }
        })
        ->setTotalRecords($count)
        ->setFilteredRecords($count)
        ->skipPaging()
        ->rawColumns(['map','action'])
        ->make(true);
    }


    //boundary map
    public function boundaryMap($id){
        $id = $id;
        $location = Hunt::where('_id',$id)
                                ->with(['hunt_complexities'=>function($query){
                                    $query->whereHas('hunt_clues');
                                }])
                                ->first();

        $complexityarr = [];
        foreach ($location->hunt_complexities as $key => $complexity) {
            $complexityarr[] = $complexity['complexity'];
        }

        return view('admin.maps.boundary',compact('location','complexityarr'));
    }

     //boundary map
    public function starComplexityMap($id,$complexity){
        $id = $id;
        $location = Hunt::where('_id',$id)
                        ->with(['hunt_complexities'=>function($query) use ($complexity){
                            $query->where('complexity',(int)$complexity);
                        }])
                        ->first();
        $complexitySuf = $this->addOrdinalNumberSuffix($complexity);
        $complexityarr = HuntComplexity::select('id','hunt_id','complexity')
                                ->where('hunt_id',$id)
                                ->pluck('complexity')
                                ->toArray();
        $games = Game::whereHas('game_variation')
                        ->with('game_variation:_id,variation_name,game_id,status')
                        ->where('status',true)
                        ->get();

        
        
        $cluesCoordinates = [];
        if (!empty($location->hunt_complexities[0]->hunt_clues)) {
            foreach($location->hunt_complexities[0]->hunt_clues as $clues){
                $cluesCoordinates[] = [$clues->location['coordinates'][0],$clues->location['coordinates'][1]];
            }
        }
        // echo "<pre>";
        // print_r($games->toArray());
        // exit();
        // $clueLocation = 
        return view('admin.maps.start_complexity',compact('location','complexity','complexitySuf','id','complexityarr','games','cluesCoordinates'));
    }

    //GET VARIATION
    public function getGameVariations(Request $request){
        $gameId = $request->get('game_id');
        $arrayId = $request->get('array_id');
        $gameVariation = GameVariation::select('variation_name','game_id')
                                        ->where('game_id',$gameId)
                                        ->get();
        return response()->json([
            'status'   => true,
            'message'  => 'data found',
            'data'     => $gameVariation,
            'array_id' => $arrayId,
        ]);
    }

    //boundary map
    public function storeStarComplexity(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_id'   => 'required',
                        'game_id.*'   => 'required',
                        'game_variation_id.*' => 'required',
                        'coordinates'=> 'required|json',
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $id = $request->get('hunt_id');
        $complexity = (int)$request->get('complexity');
        $gameId = $request->get('game_id');
        $gameVariationId = $request->get('game_variation_id');
        $hunt = Hunt::where('_id',$id)->first();
        $coordinates = json_decode($request->get('coordinates'));
        $locationdata = [];
        
        if($complexity == 1){
            $distance = 50*count($coordinates);
        } elseif($complexity == 2){
            $distance = 100*count($coordinates);
        } elseif($complexity == 3){
            $distance = 250*count($coordinates);
        } elseif($complexity == 4){
            $distance = 500*count($coordinates);
        } elseif($complexity == 5){
            $distance = 1000*count($coordinates);
        }

        foreach ($coordinates as $key => $value) {
            $location['Type'] = 'Point';
            $location['coordinates'] = [
                                            $value[0],
                                            $value[1]
                                        ];
            /** est time **/
            $km = $distance/1000;
            //4.5 km = 6o min
            // $avg_km = $km/4.5;   
            $mins = 60/4.5 * $km;
            $fixClueMins = count($coordinates)*5;
            $estTime =  $mins + $fixClueMins;
            /** end est time **/

            $huntComplexities = $hunt->hunt_complexities()->updateOrCreate(['hunt_id'=>$id,'complexity'=>$complexity],['hunt_id'=>$id,'complexity'=>$complexity,'est_completion'=>(int)round($estTime),'distance'=>$distance]);

            $huntComplexities->hunt_clues()->updateOrCreate([
                                'hunt_complexity_id' =>  $huntComplexities->_id,
                                'location.coordinates.0' =>  $value[0],
                                'location.coordinates.1' =>  $value[1],
                            ],[
                                'hunt_complexity_id' => $huntComplexities->_id,
                                'location'           => $location,
                                'game_id'            => $gameId[$key],
                                'game_variation_id'  => $gameVariationId[$key],
                            ]);
        }

        return response()->json([
            'status' => true,
            'message'=>'Clues has been added successfully inside the selected hunt.',
        ]);
    }

    public function clearAllClues($id){
        $location = Hunt::where('_id',$id)
                                ->first();
        foreach ($location->hunt_complexities() as $key => $complexity) {
            $complexity->hunt_clues()->delete();
        }
        $location->hunt_complexities()->delete();
        return response()->json([
            'status' => true,
            'message'=>'Clues has been cleared from map successfully.',
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
        $location = Hunt::where('_id',$id)
                                ->with(['hunt_complexities'=>function($query){
                                    $query->whereHas('hunt_clues');
                                  }])
                                ->first();

        $complexityarr = [];
        foreach ($location->hunt_complexities as $key => $complexity) {
            $complexityarr[] = $complexity['complexity'];
        }

        return view('admin.maps.edit_location',compact('location','complexityarr'));
    }

    //UPDATE LOCATION
    public function updateLocation(Request $request){
        $id = $request->get('id');
        $location = Hunt::where('_id',$id)
                                    ->first();

        $boundaries = $request->get('boundaries_arr');
        if(isset($boundaries) && $boundaries!=""){
            $boundaries = str_replace('[[','' , $request->get('boundaries_arr'));
            $boundaries = str_replace(']]','' , $boundaries);
            $boundaries = explode('],[', $boundaries);
            $boundaries_arr = [];
            foreach ($boundaries as $key => $value) {
                $boundaries_arr[] = array_map('floatval', explode(',',$value));
            }
            $location['boundaries_arr'] = $boundaries_arr;
        }
        

        $location->name = $request->get('name');
        $location->fees = $request->get('fees');
        $location->update();

        return response()->json([
            'status' => true,
            'message'=>'Treasure hunt location has been updated successfully.',
        ]);
    }


    //STORE LOCATION
    public function storeLocation(Request $request){
        $validator = Validator::make($request->all(),[
                        'name'  => 'required',
                        'boundary_arr' => 'required',
                        'place_name'   => 'required',
                        'latitude'     => 'required',
                        'longitude'    => 'required',
                        'city'         => 'required',
                        'province'     => 'required',
                        'country'      => 'required',
                        'fees'         => 'required',
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        $data = $request->all();
        
        // $cityInfo = TreasureLocation::get();
        // foreach ($cityInfo as $key => $value) {
        //     $hunt['custom_name'] = $value->custom_name;
        //     $location['Type'] = 'Point';
        //     $location['coordinates'] = [
        //                                     'lng' => (float)$value->longitude,
        //                                     'lat' => (float)$value->latitude
        //                                 ];
        //     $hunt['location'] = $location;
        //     $hunt['city'] = $value->city;
        //     $hunt['place_name'] = $value->place_name;
        //     $hunt['province'] = $value->province;
        //     $hunt['country'] = $value->country;
        //     $hunt['boundaries_arr'] = $value->boundary_arr;
        //     $hunt['boundingbox'] = $value->boundingbox;
        //     Hunt::create($hunt);
        // }

        $location['Type'] = 'Point';
        $location['coordinates'] = [
                                        (float)$request->get('longitude'),
                                        (float)$request->get('latitude')
                                    ];

        $data['location'] = $location;
        $boundaries = str_replace('[[','' , $request->get('boundary_arr'));
        $boundaries = str_replace(']]','' , $boundaries);
        $boundaries = explode('],[', $boundaries);
        $boundaries_arr = [];
        foreach ($boundaries as $key => $value) {
            $boundaries_arr[] = array_map('floatval', explode(',',$value));
        }

        $data['boundaries_arr'] = $boundaries_arr;
        $data['fees'] = (float)$request->get('fees');

        $boundingbox = $request->get('boundary_box');
        $data['boundingbox'] = array_map('strval', array_values(json_decode($request->get('boundary_box'),true)));
        $location = Hunt::create($data);
        return response()->json([
            'status' => true,
            'message'=>'Treasure hunt location has been created successfully.',
            'id'     => $location->_id,
        ]);
    }

    //LOCATION DELETE
    public function locationDelete($id){
        //Hunt::where('_id', $id)->delete();
        $location = Hunt::where('_id',$id)->first();
        foreach ($location->hunt_complexities as $key => $complexity) {
            $complexity->hunt_clues()->delete();
        }
        $location->hunt_complexities()->delete();
        $location->delete();

        return response()->json([
            'status' => true,
            'message'=>'Treasure hunt location has been deleted successfully.',
        ]);
    }

    //REMOVE STAR
    public function removeStar(Request $request){
        $id = $request->get('id');
        $complexity = $request->get('complexity');
        $placeStar = HuntComplexity::where([
                            'hunt_id'   => $id,
                            'complexity' => (int)$complexity,
                        ])
                        ->first();
        
        $placeStar->hunt_clues()->delete();
        $placeStar->delete();
        return response()->json([
            'status' => true,
            'message'=>'Clue deleted successfully',
        ]);
    }

    //TEST 
    public function testLocation(Request $request){
        return view('admin.maps.test_location');        
    }

    //CUSTOM STORE
    public function customRecordStore(){
        
        /*$hunts = Hunt::get();
        foreach ($hunts as $key => $hunt) {
            $hunt->name = ($hunt->name!="")?$hunt->name:$hunt->place_name;
            $hunt->save();
        }*/

        /* HUNT RECORD STORE */
        //$cityInfo = TreasureLocation::get();

        /*foreach ($cityInfo as $key => $value) {
            $location['Type'] = 'Point';
            $location['coordinates'] = [
                                            (float)$value->longitude,
                                            (float)$value->latitude
                                        ];
            $objectID = new ObjectID($value->_id);
            $hunt['_id'] = $objectID;
            $hunt['name'] = $value->place_name;
            $hunt['location'] = $location;
            $hunt['city'] = $value->city;
            $hunt['place_name'] = $value->place_name;
            $hunt['province'] = $value->province;
            $hunt['country'] = $value->country;
            $hunt['boundaries_arr'] = $value->boundary_arr[0];
            $hunt['boundingbox'] = $value->boundingbox;
            
            Hunt::create($hunt);
        }*/


        //HuntComplexitie STORE
        // $huntComplexities = PlaceStar::whereHas('place')
        //                             ->with('place_clues')
        //                             ->get();
       

        // foreach ($huntComplexities as $key => $value) {
        //     $huntComplexitie = HuntComplexitie::updateOrCreate(['hunt_id'=>$value->place_id,'complexity'=>$value->complexity],['hunt_id'=>$value->place_id,'complexity'=>$value->complexity]);

        //     foreach ($value->place_clues['coordinates'] as $key1 => $clues) {
        //         $location['Type'] = 'Point';
        //         $location['coordinates'] = [
        //                                         $clues[0],
        //                                         $clues[1]
        //                                     ];
        //         $huntComplexitie->hunt_clues()->updateOrCreate([
        //                             'hunt_complexity_id' =>  $huntComplexitie->_id,
        //                             'location.coordinates' =>  $clues[0],
        //                             'location.coordinates' =>  $clues[1],
        //                         ],[
        //                             'hunt_complexity_id' => $huntComplexitie->_id,
        //                             'location'           => $location,
        //                             'game_id'            => null,
        //                             'game_variation_id'  => null
        //                         ]);
        //     }
        // }
         return response()->json([
            'status' => true,
            'message'=>'successfully',
        ]);
    }
}
