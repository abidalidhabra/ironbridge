<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v1\Game;
use App\Models\v1\City;
use App\Models\v2\Event;
use App\Models\v2\Hunt;
use Validator;
use Image;
use MongoDB\BSON\ObjectID;
use Auth;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.event.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $games = Game::where('status',true)->get();
        $cities = City::select('name')->get();

        return view('admin.event.add_event.basic_details',compact('games','cities'));
    }

    public function basicDetails($id=NULL)
    {   
        $event = Event::where('_id',$id)->first();
        $games = Game::where('status',true)->get();
        $cities = City::select('name')->get();

        return view('admin.event.add_event.basic_details',compact('games','cities','event'));
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
            'name'             => 'required',
            'type'             => 'required|in:single,multi',
            'coin_type'        => 'required|in:ar,physical',
            'game_id.*.*'      => 'required',
            'city_id.*'        => 'required',
            'event_start_date' => 'required',
            'event_end_date'   => 'required',
            'rejection_ratio'  => 'required|integer',
            'winning_ratio'    => 'required|integer',
            'fees'             => 'required|numeric',
            'start_date.*'     => 'required',
            'end_date.*'       => 'required',
            'row.*.*'          => 'required|integer',
            'column.*.*'       => 'required|integer',
            'target.*.*'       => 'required|integer',
            'variation_image.*.*' => 'required|mimes:jpeg,jpg,png',
            'map_reveal_date.*.*' => 'required',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();
        $data['fees'] = (int)$data['fees']; 
        $data['rejection_ratio'] = (int)$data['rejection_ratio']; 
        $data['winning_ratio'] = (int)$data['winning_ratio']; 
        $data['starts_at'] =  Carbon::parse($data['event_start_date'])->format('Y-m-d H:i:s');
        $data['ends_at'] =  Carbon::parse($data['event_end_date'])->format('Y-m-d H:i:s');
        $data['map_reveal_at'] =  Carbon::parse($data['event_end_date'])->format('Y-m-d H:i:s');

        $main_game = [];


        for ($i=0; $i<count($data['game_id']) ; $i++) { 
            $game = []; 

            for ($k=0; $k < count($data['game_id'][$i])  ; $k++) { 
                $gameDetail = Game::find($data['game_id'][$i][$k]);
                
                $gameInfo = ['id'=> $data['game_id'][$i][$k], 'name'=> $gameDetail->name];
                
                /* IMAGES */
                $variationImage = $request->file('variation_image');

                if(isset($variationImage[$i][$k])){
                    $gameId = $data['game_id'][$i][$k];
                    $image = $variationImage[$i][$k];
                    $imageSize = getimagesize($image);
                    if ($gameId == '5b0e306951b2010ec820fb4f' || $gameId == '5b0e304b51b2010ec820fb4e') {
                        if ($gameId == '5b0e306951b2010ec820fb4f' && $imageSize[0] != '1024' && $imageSize[1] != '1024') {
                            return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                        }

                        if ($gameId == '5b0e304b51b2010ec820fb4e' && $imageSize[0] != '2000' && $imageSize[1] != '1440') {
                            return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                        }
                    }

                    $extension = $image->getClientOriginalExtension();
                    $img = Image::make($image);
                    $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
                    $img->save(storage_path('app/public/events/'.$imageUniqueName));
                    $gameInfo['variation_image'] = $imageUniqueName;
                }


                $variation_data = [
                    'row'    => $data['row'][$i][$k], 
                    'column' => $data['column'][$i][$k],
                    'target' => $data['target'][$i][$k]
                ];
               
                $game[] = [
                    '_id'            => new ObjectID(),
                    'game_info'      => $gameInfo,
                    'variation_data' => $variation_data
                ];
            }
                // print_r($gameInfo);
                // exit();
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i])));
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['end_date'][$i])));
            $main_game[] = [
                        //'from'  => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i]))), 
                        'from'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($startDate)), 
                        'to'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($endDate)),  
                        'games' => $game, 
                    ];

        }
        $data['mini_games'] = $main_game;
        
        $evert = Event::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Basic details added successfully.',
            'id'      => $evert->id
        ]);
    }

    public function addBasicStore(Request $request){
        $validator = Validator::make($request->all(),[
            'name'             => 'required',
            'type'             => 'required|in:single,multi',
            'coin_type'        => 'required|in:ar,physical',
            'city_id.*'        => 'required',
            'event_start_date' => 'required',
            'event_end_date'   => 'required',
            'rejection_ratio'  => 'required|integer',
            'winning_ratio'    => 'required|integer',
            'fees'             => 'required|numeric',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $data = $request->all();
        $eventId = $data['event_id']; 
        $data['fees'] = (int)$data['fees']; 
        $data['rejection_ratio'] = (int)$data['rejection_ratio']; 
        $data['winning_ratio'] = (int)$data['winning_ratio']; 
        $data['starts_at'] =  Carbon::parse($data['event_start_date'])->format('Y-m-d H:i:s');
        $data['ends_at'] =  Carbon::parse($data['event_end_date'])->format('Y-m-d H:i:s');

        $event = Event::updateOrCreate(
                                        ['_id'=>$eventId],
                                        $data
                                    );

        return response()->json([
            'status'  => true,
            'message' => 'Basic details added successfully.',
            'id'      => $event->id
        ]);
    }

    public function miniGame($id){
        $games = Game::where('status',true)->get();
        $cities = City::select('name')->get();
        $event = Event::where('_id',$id)->first();
        return view('admin.event.add_event.mini_game',compact('id','games','cities','event'));
    }

    public function addMiniGame(Request $request){
        $validator = Validator::make($request->all(),[
            'game_id.*.*'      => 'required',
            'row.*.*'          => 'required|integer',
            'column.*.*'       => 'required|integer',
            'target.*.*'       => 'required|integer',
            'variation_image.*.*' => 'required|mimes:jpeg,jpg,png',
            'map_reveal_date.*.*' => 'required',
            'variation_size.*.*'  => 'required|integer',
            // 'variation_name.*.*'  => 'required',
            // 'variation_complexity.*.*'  => 'required',
            'number_generate.*.*'  => 'required|integer',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();

        $main_game = [];

        print_r('hello');
        exit();
        for ($i=0; $i<count($data['game_id']) ; $i++) { 
            $game = []; 

            $gameData = array_values($data['game_id'][$i]);
            $variationImage = $request->file('variation_image');

            for ($k=0; $k < count($gameData)  ; $k++) { 
                
                $gameDetail = Game::find($gameData[$k]);
                
                $gameInfo = ['id'=> $gameData[$k], 'name'=> $gameDetail->name];
                
                /* IMAGES */

                if(isset($variationImage) && !empty(array_values($variationImage[$i])[$k])){
                    $gameId = $gameData[$k];
                    $image = array_values($variationImage[$i])[$k];
                    $imageSize = getimagesize($image);
                    if ($gameId == '5b0e306951b2010ec820fb4f' || $gameId == '5b0e304b51b2010ec820fb4e') {
                        if ($gameId == '5b0e306951b2010ec820fb4f' && $imageSize[0] != '1024' && $imageSize[1] != '1024') {
                            return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                        }

                        if ($gameId == '5b0e304b51b2010ec820fb4e' && $imageSize[0] != '2000' && $imageSize[1] != '1440') {
                            return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                        }
                    }

                    $extension = $image->getClientOriginalExtension();
                    $img = Image::make($image);
                    $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
                    $img->save(storage_path('app/public/events/'.$imageUniqueName));
                    $gameInfo['variation_image'] = $imageUniqueName;
                } else {
                    if(isset($data['hide_image'][$i]) && isset(array_values($data['hide_image'][$i])[$k])){
                        $gameInfo['variation_image'] = array_values($data['hide_image'][$i])[$k];
                    }
                    
                }


                $variation_data = [
                    'row'    => array_values($data['row'][$i])[$k], 
                    'column' => array_values($data['column'][$i])[$k],
                    'target' => array_values($data['target'][$i])[$k]
                ];
               
                $game[] = [
                    '_id'            => new ObjectID(),
                    'game_info'      => $gameInfo,
                    'variation_data' => $variation_data
                ];
            }
                
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i])));
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['end_date'][$i])));
            $main_game[] = [
                        //'from'  => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i]))), 
                        'from'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($startDate)), 
                        'to'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($endDate)),  
                        'games' => $game, 
                    ];

        }
        $data['mini_games'] = $main_game;
        

        $eventId = $request->get('event_id');

        $evert = Event::where('_id',$eventId)->first();
        $evert->mini_games = $data['mini_games'];
        $evert->save();

        return response()->json([
            'status'  => true,
            'message' => 'Mini game added successfully.',
            'id'      => $eventId
        ]);
    }

    public function huntDetails($id){
        $event = Event::where('_id',$id)->with('city:_id,name')->first();
        
        $hunts = Hunt::select('name','place_name','city')
                        ->where('city',$event->city->name)
                        ->get();
        
        return view('admin.event.add_event.hunt_details',compact('id','hunts','event'));        
    }

    public function addHuntDetails(Request $request){
        $validator = Validator::make($request->all(),[
            'map_reveal_date'   => 'required',
            'search_place_name' => 'required|exists:hunts,_id',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $eventId = $request->get('event_id');
        $event = Event::where('_id',$eventId)->first();
        $event->map_reveal_date = Carbon::parse($request->get('map_reveal_date'))->format('Y-m-d H:i:s'); 
        $event->hunt_id = $request->get('search_place_name');
        $event->save();

        return response()->json([
            'status'  => true,
            'message' => 'successfully hunt added successfully.',
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
        $games = Game::where('status',true)->get();
        $cities = City::select('name')->get();
        $event = Event::where('_id',$id)->first();

        return view('admin.event.edit_event',compact('games','cities','event'));
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
        Event::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'Event has been deleted successfully.',
        ]);
    }


    public function getEventList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $events = Event::select('name','type','coin_type','rejection_ratio','winning_ratio','city_id','fees','starts_at','ends_at','mini_games');
        $admin = Auth::user();

        if($search != ''){
            $events->where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('type','like','%'.$search.'%')
                ->orWhere('coin_type','like','%'.$search.'%')
                ->orWhere('rejection_ratio','like','%'.$search.'%')
                ->orWhere('winning_ratio','like','%'.$search.'%');
            });
        }

        $events = $events->with('city:_id,name')->orderBy('created_at','DESC')->skip($skip)->take($take)->get();

        

        $count = Event::count();
        if($search != ''){
            $count = Event::where(function($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                ->orWhere('type','like','%'.$search.'%')
                ->orWhere('coin_type','like','%'.$search.'%')
                ->orWhere('rejection_ratio','like','%'.$search.'%')
                ->orWhere('winning_ratio','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($events)
        ->addIndexColumn()
        ->addColumn('city',function($event){
            return $event->city->name;
        })
        ->addColumn('clue',function($event){
            return '<a href="'.route('admin.boundary_map',$event->id).'" ><img src="'.asset('admin_assets/svg/map-marke-icon.svg').'"</a>';
        })
        ->addColumn('action', function($query) use ($admin){
            $data = '';
                $data .=  '<a href="'.route('admin.event.show',$query->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            
                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$query->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
                </a>';
            

            return $data;
        })
        ->rawColumns(['action','city','clue'])
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