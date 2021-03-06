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
use App\Models\v2\EventsPrize;

class EventControllerOld extends Controller
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
            'coin_number.*.*' => 'integer',

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
        

        $event_days = [];


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

                        if ($gameId == '5b0e304b51b2010ec820fb4e' && $imageSize[0] != '1440' && $imageSize[1] != '2000') {
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
            $event_days[] = [
                        //'from'  => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i]))), 
                'from'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($startDate)), 
                'to'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($endDate)),  
                'mini_games' => $game, 
            ];

        }
        $data['event_days'] = $event_days;
        
        $evert = Event::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Basic details added successfully.',
            'id'      => $evert->id
        ]);
    }

    public function addBasicStore(Request $request){
        
        if (isset($request['winning_ratio_check'])) {
            $request['winning_ratio_check'] = 'true';
        } else {
            $request['winning_ratio_check'] = 'false';
        }

        if (isset($request['rejection_ratio_check'])) {
            $request['rejection_ratio_check'] = 'true';
        } else {
            $request['rejection_ratio_check'] = 'false';
        }

        if (isset($request['participation_check'])) {
            $request['participation_check'] = 'true';
        } else {
            $request['participation_check'] = 'false';
        }

        $validator = Validator::make($request->all(),[
            'name'             => 'required',
            'type'             => 'required|in:single,multi',
            'coin_type'        => 'required|in:ar,physical',
            'city_id'          => 'required',
            'event_start_date' => 'required',
            'event_end_date'   => 'required',
            // 'rejection_ratio'  => 'required|integer',
            // 'winning_ratio'    => 'required|integer',
            'rejection_ratio'  => 'required_if:rejection_ratio_check,false',
            'winning_ratio'    => 'required_if:winning_ratio_check,false',
            'participation'    => 'required_if:participation_check,false',
            'fees'             => 'required|numeric',
            'coin_number'      => 'required_if:coin_type,physical',
            'discount_date'    => 'required',
            'discount_fees'    => 'required|numeric|min:0|max:100',
            'description'      => 'required',
            'attempts'         => 'required|integer',

        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $data                    = $request->all();
        $eventId                 = $data['event_id']; 
        $data['fees']            = (int)$data['fees']; 
        $data['rejection_ratio'] = (isset($data['rejection_ratio'])?(int)$data['rejection_ratio']:null); 
        $data['winning_ratio']   = (isset($data['winning_ratio'])?(int)$data['winning_ratio']:null); 
        $data['participation']   = (isset($data['participation'])?(int)$data['participation']:null); 
        $data['starts_at']       = Carbon::parse($data['event_start_date'])->format('Y-m-d H:i:s');
        $data['ends_at']         = Carbon::parse($data['event_end_date'])->endOfDay()->format('Y-m-d H:i:s');
        $data['coin_number']     = (int)$data['coin_number'];
        $data['discount']        = (float)$data['discount_fees'];
        $data['discount_till']   = Carbon::parse($data['discount_date'])->format('Y-m-d H:i:s');
        $data['attempts']        = (int)$data['attempts'];
        $data['status']          = 'inactive';

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
            'game_id.*.*'         => 'required',
            'row.*.*'             => 'required|integer',
            'column.*.*'          => 'required|integer|same:row.*.*',
            'target.*.*'          => 'required|integer',
            'variation_image.*.*' => 'required|mimes:jpeg,jpg,png',
            'map_reveal_date.*.*' => 'required',
            'variation_size.*.*'  => 'required|integer',
            // 'variation_name.*.*'  => 'required',
            // 'variation_complexity.*.*'  => 'required',
            'number_generate.*.*'  => 'required|integer',
            'start_time.*'         => 'required',
            'end_time.*'           => 'required',
            'date.*'               => 'required',
            'no_of_balls.*.*'  => 'required|integer',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        $data = $request->all();
        $eventId = $request->get('event_id');
        $event = Event::where('_id',$eventId)->first();

        $totalDay = $event->starts_at->diffInDays($event->ends_at->addDays('1'));
        
        if ($event->type == 'multi') {
            if($totalDay != count($data['game_id'])){
                return response()->json(['status' => false,'message' => 'Please manage the  day '.$totalDay]);
            }
        }

        $event_days = [];
        
        
        $gameCount = last(array_keys($data['game_id']))+1;
        
        $p = 1;
        for ($i=0; $i<$gameCount ; $i++) { 
            if (isset($data['game_id'][$i])) {

                $gameData = $data['game_id'][$i];
                
                $variationImage = $request->file('variation_image');

                $game = []; 
                $miniGameCount = last(array_keys($gameData))+1;
                for ($k=0; $k < $miniGameCount  ; $k++) {
                    if (isset($gameData[$k])) {                         
                        $variation_data = [];
                        
                        $gameDetail = Game::find($gameData[$k]);
                        
                        if ($gameDetail->identifier == 'sudoku') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'number_search') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'jigsaw') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'sliding') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'word_search') {
                            $basedOn = '';
                        } else if ($gameDetail->identifier == '2048') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'block') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'hexa') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'bubble_shooter') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'snake') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'domino') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'slices') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'yatzy') {
                            $basedOn = 'score';
                        }

                        $gameInfo = [
                                    'id'         => $gameData[$k],
                                    'name'       => $gameDetail->name,
                                    'identifier' => $gameDetail->identifier,
                                    'based_on'   => $basedOn
                                ];
                        

                        /* IMAGES */
                        $gameId = $gameData[$k];
                        if (($gameId == '5b0e306951b2010ec820fb4f' || $gameId == '5b0e304b51b2010ec820fb4e') && isset($variationImage)) {

                            $variationImageIndex = last(array_keys($variationImage[$i]));
                            
                            if(isset($variationImage[$i][$k])){
                                $image = $variationImage[$i][$k];
                                $imageSize = getimagesize($image);
                                
                                if ($gameId == '5b0e306951b2010ec820fb4f' && $imageSize[0] != 1024 && $imageSize[1] != 1024) {
                                    return response()->json(['status' => false,'message' => 'The variation image size is invalid1.']);
                                }

                                if ($gameId == '5b0e304b51b2010ec820fb4e' && $imageSize[0] != 2000 && $imageSize[1] != 1440) {
                                    return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                                }

                                $extension = $image->getClientOriginalExtension();
                                $img = Image::make($image);
                                $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
                                $img->save(storage_path('app/public/events/'.$imageUniqueName));
                                $variation_data['variation_image'] = $imageUniqueName;
                            } else {
                                if(isset($data['hide_image']) && isset($data['hide_image'][$i][$k])){
                                    $variation_data['variation_image'] = $data['hide_image'][$i][$k];
                                }    
                            }
                            
                        } else {
                            /*if(isset($data['hide_image']) && isset($data['hide_image'][$i][$k])){
                                $variation_data['variation_image'] = $data['hide_image'][$i][$k];
                            }*/
                        }

                        if (isset($data['row']) && isset($data['row'][$i][$k])) {
                            $variation_data['row'] = (int)$data['row'][$i][$k];
                        }

                        if (isset($data['column']) && isset($data['column'][$i][$k])) {
                            $variation_data['column'] = (int)$data['column'][$i][$k];
                        }

                        if (isset($data['target']) && isset($data['target'][$i][$k])) {
                            $variation_data['target'] = (int)$data['target'][$i][$k];
                        }

                        // if (isset($data['target']) && isset($data['row'][$i][$k])) {
                        //     $variation_data['target'] = (int)$data['column'][$i][$k];
                        // }

                        if (isset($data['no_of_balls']) && isset($data['no_of_balls'][$i][$k])) {
                            $variation_data['no_of_balls'] = (int)$data['no_of_balls'][$i][$k];
                        }

                        if (isset($data['bubble_level_id']) && isset($data['bubble_level_id'][$i][$k])) {
                            $variation_data['bubble_level_id'] = (int)$data['bubble_level_id'][$i][$k];
                        }

                        if (isset($data['variation_size']) && isset($data['variation_size'][$i][$k])) {
                            $variation_data['variation_size'] = (int)$data['variation_size'][$i][$k];
                        }

                        // if (isset($data['default_reveal_number']) && isset($data['default_reveal_number'][$i][$k])) {
                        //     $variation_data['default_reveal_number'] = (int)$data['default_reveal_number'][$i][$k];
                        // }

                        if (isset($data['number_generate']) && isset($data['number_generate'][$i][$k])) {
                            $variation_data['number_generate'] = (int)$data['number_generate'][$i][$k];
                        }

                        if (isset($data['sudoku_id']) && isset($data['sudoku_id'][$i][$k])) {
                            $variation_data['sudoku_id'] = (int)$data['sudoku_id'][$i][$k];
                        }

                        /*$variation_data = [
                            'row'    => array_values($data['row'][$i])[$k], 
                            'column' => array_values($data['column'][$i])[$k],
                            'target' => array_values($data['target'][$i])[$k]
                        ];*/
                        
                        $game[] = [
                            '_id'            => new ObjectID(),
                            'game_info'      => $gameInfo,
                            'variation_data' => $variation_data
                        ];
                    }
                }
                
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['date'][$i].' '.$data['start_time'][$i])));
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['date'][$i].' '.$data['end_time'][$i])));
                
                $event_days[] = [
                            //'from'  => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i]))), 
                    'from'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($startDate)), 
                    'to'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($endDate)),  
                    'mini_games' => $game,
                    'day'   => $p 
                ];
                $p++;
            }

        }
        
        $data['event_days'] = $event_days;
        
        $event->event_days = $data['event_days'];
        $event->status = 'inactive';
        $event->save();

        return response()->json([
            'status'  => true,
            'message' => 'Mini game added successfully.',
            'id'      => $eventId
        ]);
    }

    public function huntDetails($id){
        $event = Event::where('_id',$id)
        ->with('prizes')
        // ->with('event_map_time_delay')
        ->with('city:_id,name')
        ->first();
        
        $hunts = Hunt::select('name','place_name','city')
        ->where('city',$event->city->name)
        ->get();

        return view('admin.event.add_event.hunt_details',compact('id','hunts','event'));        
    }

    public function addHuntDetails(Request $request){
        $validator = Validator::make($request->all(),[
            'map_reveal_date'       => 'required',
            'search_place_name'     => 'required|exists:hunts,_id',
            'group_type.*'          => 'required',
            'prize.*'               => 'required|integer',
            'rank.*'                => 'required|integer',
            'start_rank.*'          => 'required|integer',
            'end_rank.*'            => 'required|integer',
            'prize_type.*'          => 'required',
            'map_time_delay.*'      => 'required',
            'map_time_group_type.*' => 'required',
            'map_time_rank.*'       => 'required|integer',
            'map_time_start_rank.*' => 'required|integer',
            'map_time_end_rank.*'   => 'required|integer',
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $eventId = $request->get('event_id');
        $event = Event::where('_id',$eventId)
                    ->with('prizes')
                    ->first();
        $event->map_reveal_date = Carbon::parse($request->get('map_reveal_date'))->format('Y-m-d H:i:s'); 
        $event->hunt_id = $request->get('search_place_name');
        $event->status = 'active';
        
        /* PRIZE VAR */
        $groupType = $request->get('group_type');
        $prize = $request->get('prize');
        $rank = $request->get('rank');
        $startRank = $request->get('start_rank');
        $endRank = $request->get('end_rank');
        $prizeType = $request->get('prize_type');
        $totalPrizeIndex = last(array_keys($groupType))+1;
        /* END PRIZE VAR */

        /* MAP TIME DELAY VAR */
        $mapTimeDelay = $request->get('map_delay_time');

        $mapRank = $request->get('map_time_rank');
        $mapStartrank = $request->get('map_time_start_rank');
        $mapEndRank = $request->get('map_time_end_rank');
        $mapGroupType = $request->get('map_time_group_type');
        $totalMapTimeIndex = last(array_keys($mapGroupType))+1;

        /* END MAP TIME DELAY VAR */

        $allRank = array_merge((isset($rank))?$rank:[],(isset($startRank))?$startRank:[],(isset($endRank))?$endRank:[]);
        
        if (isset($event->winning_ratio) && $event->winning_ratio < max($allRank)) {
            return response()->json(['status' => false,'message' => 'Please manage prize rank in max '.$event->winning_ratio]);
        }
        
        $mapTimeAllRank = array_merge((isset($mapRank))?$mapRank:[],(isset($mapStartrank))?$mapStartrank:[],(isset($mapEndRank))?$mapEndRank:[]);
        if (max($allRank) < max($mapTimeAllRank)) {
            return response()->json(['status' => false,'message' => 'Please manage Map time delay rank in max '.max($allRank)]);            
        }
        if (min($allRank) > min($mapTimeAllRank)) {
            return response()->json(['status' => false,'message' => 'Please manage Map time delay rank in min '.min($allRank)]);            
        }

        /* MAP TIMR DELAY EVENT MODULE STORE */
        $mapTimeDelayArray = [];
        for ($m=0; $m < $totalMapTimeIndex ; $m++) { 
            
            if (isset($mapGroupType[$m])) {
                $data = [];
                $data['group_type']     = $mapGroupType[$m];
                $data['map_delay_time'] = (int)$mapTimeDelay[$m]*60;

                if (isset($mapRank[$m])) {
                    $data['rank'] = (int)$mapRank[$m];
                } 

                if(isset($mapStartrank[$m])){
                    $data['start_rank'] = (int)$mapStartrank[$m];
                    $data['end_rank'] = (int)$mapEndRank[$m];
                }
                $mapTimeDelayArray[] =$data;
            }
        }
        $event->map_delay_time = $mapTimeDelayArray; 
        /* END MAP TIMR DELAY EVENT MODULE STORE */
        
        $event->save();
                
        /* PRIZES ALL DELETE */
        $event->prizes()->delete();
        
        for ($i=0; $i < $totalPrizeIndex ; $i++) { 
            
            if (isset($groupType[$i])) {
                $data = [];
                $data['event_id']       = $eventId;
                $data['group_type']     = $groupType[$i];
                $data['prize_type']     = $prizeType[$i];
                $data['prize_value']    = (int)$prize[$i];
                // $data['map_time_delay'] = (int)$mapTimeDelay[$i];

                if (isset($rank[$i])) {
                    $data['rank'] = (int)$rank[$i];
                } 

                if(isset($startRank[$i])){
                    $data['start_rank'] = (int)$startRank[$i];
                    $data['end_rank'] = (int)$endRank[$i];
                }
                $event->prizes()->create($data);
            }
        }
        /* END PRIZE */



        /*Map TIME DELAY */
        // $event->event_map_time_delay()->delete();
        
        // for ($i=0; $i < $totalMapTimeIndex ; $i++) { 
            
        //     if (isset($groupType[$i])) {
        //         $data = [];
        //         $data['event_id']       = $eventId;
        //         $data['group_type']     = $mapGroupType[$i];
        //         $data['prize_value']    = (int)$prize[$i];
        //         $data['map_time_delay'] = (int)$mapTimeDelay[$i]*60;

        //         if (isset($mapRank[$i])) {
        //             $data['rank'] = (int)$mapRank[$i];
        //         } 

        //         if(isset($mapStartrank[$i])){
        //             $data['start_rank'] = (int)$mapStartrank[$i];
        //             $data['end_rank'] = (int)$mapEndRank[$i];
        //         }
        //         $event->event_map_time_delay()->create($data);
        //     }
        // }
        
        /*END MAP TIME DELAY*/
        // print_r($request->all());
        return response()->json([
            'status'  => true,
            'message' => 'Data added successfully.',
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
        $event = Event::where('_id',$id)
        ->with('city:_id,name')
        ->with('prizes')
        ->first();
        // $event = Event::where('_id',$id)->first();
        $games = Game::where('status',true)->get();
        $cities = City::select('name')->get();
        $hunts = Hunt::select('name','place_name','city')
        ->where('city',$event->city->name)
        ->get();
        return view('admin.event.edit_event',compact('id','games','cities','event','hunts'));
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

        
    }

    public function updateEvent(request $request){
        if (isset($request['winning_ratio_check'])) {
            $request['winning_ratio_check'] = 'true';
        } else {
            $request['winning_ratio_check'] = 'false';
        }

        if (isset($request['rejection_ratio_check'])) {
            $request['rejection_ratio_check'] = 'true';
        } else {
            $request['rejection_ratio_check'] = 'false';
        }

        if (isset($request['participation_check'])) {
            $request['participation_check'] = 'true';
        } else {
            $request['participation_check'] = 'false';
        }

        $validator = Validator::make($request->all(),[
            'name'             => 'required',
            'type'             => 'required|in:single,multi',
            'coin_type'        => 'required|in:ar,physical',
            'city_id'          => 'required',
            'event_start_date' => 'required',
            'event_end_date'   => 'required',
            // 'rejection_ratio'  => 'required|integer',
            // 'winning_ratio'    => 'required|integer',
            'rejection_ratio'  => 'required_if:rejection_ratio_check,false',
            'winning_ratio'    => 'required_if:winning_ratio_check,false',
            'participation'    => 'required_if:participation_check,false',
            'fees'             => 'required|numeric',
            'coin_number'      => 'required_if:coin_type,physical',
            'discount_date'    => 'required',
            'discount_fees'    => 'required|numeric|min:0|max:100',
            'description'      => 'required',
            'attempts'         => 'required|integer',
            'game_id.*.*'      => 'required',
            'row.*.*'          => 'required|integer',
            'column.*.*'       => 'required|integer|same:row.*.*',
            'target.*.*'       => 'required|integer',
            'variation_image.*.*' => 'required|mimes:jpeg,jpg,png',
            'map_reveal_date.*.*' => 'required',
            'variation_size.*.*'  => 'required|integer',
            'no_of_balls.*.*'  => 'required|integer',
            // 'variation_name.*.*'  => 'required',
            // 'variation_complexity.*.*'  => 'required',
            'number_generate.*.*'  => 'required|integer',
            'map_reveal_date'   => 'required',
            'search_place_name' => 'required|exists:hunts,_id',
            'group_type.*'      => 'required',
            'rank.*'            => 'required|integer',
            'prize.*'           => 'required|integer',
            'start_rank.*'      => 'required|integer',
            'end_rank.*'        => 'required|integer',
            'prize_type.*'      => 'required',
            'map_time_delay.*'      => 'required',
            'map_time_group_type.*' => 'required',
            'map_time_rank.*'       => 'required|integer',
            'map_time_start_rank.*' => 'required|integer',
            'map_time_end_rank.*'   => 'required|integer',
        ]);


        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $data = $request->all();
        $eventId = $request->get('event_id');
        $event = Event::where('_id',$eventId)
                        ->with('prizes')
                        ->first();

        /* BASIC  DETAILS */
        $event->fees            = (int)$data['fees']; 
        //$event->rejection_ratio = (int)$data['rejection_ratio']; 
        //$event->winning_ratio   = (int)$data['winning_ratio']; 
        $event->rejection_ratio = (isset($data['rejection_ratio'])?(int)$data['rejection_ratio']:null); 
        $event->winning_ratio   = (isset($data['winning_ratio'])?(int)$data['winning_ratio']:null);
        $event->participation   = (isset($data['participation'])?(int)$data['participation']:null);
        $event->starts_at       = Carbon::parse($data['event_start_date'])->format('Y-m-d H:i:s');
        $event->ends_at         = Carbon::parse($data['event_end_date'])->endOfDay()->format('Y-m-d H:i:s');
        $event->discount        = (float)$data['discount_fees'];
        $event->discount_till   = Carbon::parse($data['discount_date'])->format('Y-m-d H:i:s');
        $event->attempts        = (int)$data['attempts'];
        $event->coin_type       = $data['coin_type'];
        $event->name            = $data['name'];
        $event->city_id         = $data['city_id'];
        $event->description      = $data['description'];
        if ($data['coin_type'] == 'physical') {
            $event->coin_number     = (int)$data['coin_number'];
        } else {
            $event->coin_number = null;
        }
        
        /* END BASIC DETAILS */
        
        /* MINI GAME */
         $totalDay = $event->starts_at->diffInDays($event->ends_at->addDays('1'));
        
        if ($event->type == 'multi') {
            if($totalDay != count($data['game_id'])){
                return response()->json(['status' => false,'message' => 'Please manage the  day '.$totalDay]);
            }
        }

        $event_days = [];
        
        
        $gameCount = last(array_keys($data['game_id']))+1;
        
        $p = 1;
        for ($i=0; $i<$gameCount ; $i++) { 
            if (isset($data['game_id'][$i])) {

                $gameData = $data['game_id'][$i];
                
                $variationImage = $request->file('variation_image');

                $game = []; 
                $miniGameCount = last(array_keys($gameData))+1;
                for ($k=0; $k < $miniGameCount  ; $k++) {
                    if (isset($gameData[$k])) {                         
                        $variation_data = [];
                        
                        $gameDetail = Game::find($gameData[$k]);
                        
                        if ($gameDetail->identifier == 'sudoku') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'number_search') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'jigsaw') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'sliding') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'word_search') {
                            $basedOn = '';
                        } else if ($gameDetail->identifier == '2048') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'block') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'hexa') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'bubble_shooter') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'snake') {
                            $basedOn = 'time';
                        } else if ($gameDetail->identifier == 'domino') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'slices') {
                            $basedOn = 'score';
                        } else if ($gameDetail->identifier == 'yatzy') {
                            $basedOn = 'score';
                        }

                        $gameInfo = [
                                    'id'         => $gameData[$k],
                                    'name'       => $gameDetail->name,
                                    'identifier' => $gameDetail->identifier,
                                    'based_on'   => $basedOn
                                ];
                        

                        /* IMAGES */
                        $gameId = $gameData[$k];
                        if (($gameId == '5b0e306951b2010ec820fb4f' || $gameId == '5b0e304b51b2010ec820fb4e') && isset($variationImage)) {

                            $variationImageIndex = last(array_keys($variationImage[$i]));
                            
                            if(isset($variationImage[$i][$k])){
                                $image = $variationImage[$i][$k];
                                $imageSize = getimagesize($image);
                                
                                if ($gameId == '5b0e306951b2010ec820fb4f' && $imageSize[0] != 1024 && $imageSize[1] != 1024) {
                                    return response()->json(['status' => false,'message' => 'The variation image size is invalid1.']);
                                }

                                if ($gameId == '5b0e304b51b2010ec820fb4e' && $imageSize[0] != 2000 && $imageSize[1] != 1440) {
                                    return response()->json(['status' => false,'message' => 'The variation image size is invalid.']);
                                }

                                $extension = $image->getClientOriginalExtension();
                                $img = Image::make($image);
                                $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
                                $img->save(storage_path('app/public/events/'.$imageUniqueName));
                                $variation_data['variation_image'] = $imageUniqueName;
                            } else {
                                if(isset($data['hide_image']) && isset($data['hide_image'][$i][$k])){
                                    $variation_data['variation_image'] = $data['hide_image'][$i][$k];
                                }    
                            }
                            
                        } else {
                            /*if(isset($data['hide_image']) && isset($data['hide_image'][$i][$k])){
                                $variation_data['variation_image'] = $data['hide_image'][$i][$k];
                            }*/
                        }

                        if (isset($data['row']) && isset($data['row'][$i][$k])) {
                            $variation_data['row'] = (int)$data['row'][$i][$k];
                        }

                        if (isset($data['column']) && isset($data['column'][$i][$k])) {
                            $variation_data['column'] = (int)$data['column'][$i][$k];
                        }

                        if (isset($data['target']) && isset($data['target'][$i][$k])) {
                            $variation_data['target'] = (int)$data['target'][$i][$k];
                        }

                        // if (isset($data['target']) && isset($data['row'][$i][$k])) {
                        //     $variation_data['target'] = (int)$data['column'][$i][$k];
                        // }

                        if (isset($data['no_of_balls']) && isset($data['no_of_balls'][$i][$k])) {
                            $variation_data['no_of_balls'] = (int)$data['no_of_balls'][$i][$k];
                        }

                        if (isset($data['bubble_level_id']) && isset($data['bubble_level_id'][$i][$k])) {
                            $variation_data['bubble_level_id'] = (int)$data['bubble_level_id'][$i][$k];
                        }

                        if (isset($data['variation_size']) && isset($data['variation_size'][$i][$k])) {
                            $variation_data['variation_size'] = (int)$data['variation_size'][$i][$k];
                        }

                        // if (isset($data['default_reveal_number']) && isset($data['default_reveal_number'][$i][$k])) {
                        //     $variation_data['default_reveal_number'] = (int)$data['default_reveal_number'][$i][$k];
                        // }

                        if (isset($data['number_generate']) && isset($data['number_generate'][$i][$k])) {
                            $variation_data['number_generate'] = (int)$data['number_generate'][$i][$k];
                        }

                        if (isset($data['sudoku_id']) && isset($data['sudoku_id'][$i][$k])) {
                            $variation_data['sudoku_id'] = (int)$data['sudoku_id'][$i][$k];
                        }

                        /*$variation_data = [
                            'row'    => array_values($data['row'][$i])[$k], 
                            'column' => array_values($data['column'][$i])[$k],
                            'target' => array_values($data['target'][$i])[$k]
                        ];*/
                        
                        $game[] = [
                            '_id'            => new ObjectID(),
                            'game_info'      => $gameInfo,
                            'variation_data' => $variation_data
                        ];
                    }
                }
                
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['date'][$i].' '.$data['start_time'][$i])));
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['date'][$i].' '.$data['end_time'][$i])));
                
                $event_days[] = [
                            //'from'  => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($data['start_date'][$i]))), 
                    'from'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($startDate)), 
                    'to'  =>  new \MongoDB\BSON\UTCDateTime(new \DateTime($endDate)),  
                    'mini_games' => $game,
                    'day'   => $p 
                ];
                $p++;
            }

        }
        
        $data['event_days'] = $event_days;
        $event->event_days = $data['event_days'];        

        /* END MINI GAMES */

        /* HUNT AND PRIZE*/
        $event->map_reveal_date = Carbon::parse($request->get('map_reveal_date'))->format('Y-m-d H:i:s'); 
        $event->hunt_id = $request->get('search_place_name');
        $event->status = 'active';


        
            
            /* PRIZE VAR */
            $groupType = $request->get('group_type');
            $prize = $request->get('prize');
            $rank = $request->get('rank');
            $startRank = $request->get('start_rank');
            $endRank = $request->get('end_rank');
            $prizeType = $request->get('prize_type');
            $totalPrizeIndex = last(array_keys($groupType))+1;
            /* END PRIZE VAR */

            /* MAP TIME DELAY VAR */
            $mapTimeDelay = $request->get('map_delay_time');

            $mapRank = $request->get('map_time_rank');
            $mapStartrank = $request->get('map_time_start_rank');
            $mapEndRank = $request->get('map_time_end_rank');
            $mapGroupType = $request->get('map_time_group_type');
            $totalMapTimeIndex = last(array_keys($mapGroupType))+1;

            /* END MAP TIME DELAY VAR */

            $allRank = array_merge((isset($rank))?$rank:[],(isset($startRank))?$startRank:[],(isset($endRank))?$endRank:[]);
            
            if (isset($event->winning_ratio) && $event->winning_ratio < max($allRank)) {
                return response()->json(['status' => false,'message' => 'Please manage prize rank in max '.$event->winning_ratio]);
            }
            
            $mapTimeAllRank = array_merge((isset($mapRank))?$mapRank:[],(isset($mapStartrank))?$mapStartrank:[],(isset($mapEndRank))?$mapEndRank:[]);
            if (max($allRank) < max($mapTimeAllRank)) {
                return response()->json(['status' => false,'message' => 'Please manage Map time delay rank in max '.max($allRank)]);            
            }
            if (min($allRank) > min($mapTimeAllRank)) {
                return response()->json(['status' => false,'message' => 'Please manage Map time delay rank in min '.min($allRank)]);            
            }


            /* MAP TIMR DELAY EVENT MODULE STORE */
            $mapTimeDelayArray = [];
            for ($i=0; $i < $totalMapTimeIndex ; $i++) { 
                
                if (isset($mapGroupType[$i])) {
                    $data = [];
                    $data['group_type']     = $mapGroupType[$i];
                    $data['map_delay_time'] = (int)$mapTimeDelay[$i]*60;

                    if (isset($mapRank[$i])) {
                        $data['rank'] = (int)$mapRank[$i];
                    } 

                    if(isset($mapStartrank[$i])){
                        $data['start_rank'] = (int)$mapStartrank[$i];
                        $data['end_rank'] = (int)$mapEndRank[$i];
                    }
                    $mapTimeDelayArray[] =$data;
                }
            }
            $event->map_delay_time = $mapTimeDelayArray; 
            /* END MAP TIMR DELAY EVENT MODULE STORE */

            $event->save();

            /* PRIZES ALL DELETE */
            $event->prizes()->delete();
            
            for ($i=0; $i < $totalPrizeIndex ; $i++) { 
                
                if (isset($groupType[$i])) {
                    $data = [];
                    $data['event_id']       = $eventId;
                    $data['group_type']     = $groupType[$i];
                    $data['prize_type']     = $prizeType[$i];
                    $data['prize_value']    = (int)$prize[$i];
                    // $data['map_time_delay'] = (int)$mapTimeDelay[$i];

                    if (isset($rank[$i])) {
                        $data['rank'] = (int)$rank[$i];
                    } 

                    if(isset($startRank[$i])){
                        $data['start_rank'] = (int)$startRank[$i];
                        $data['end_rank'] = (int)$endRank[$i];
                    }
                    $event->prizes()->create($data);
                }
            }
            /* END PRIZE */


            /*Map TIME DELAY */
            /*$event->event_map_time_delay()->delete();
            
            for ($i=0; $i < $totalMapTimeIndex ; $i++) { 
                
                if (isset($groupType[$i])) {
                    $data = [];
                    $data['event_id']       = $eventId;
                    $data['group_type']     = $mapGroupType[$i];
                    $data['prize_value']    = (int)$prize[$i];
                    $data['map_time_delay'] = (int)$mapTimeDelay[$i]*60;

                    if (isset($mapRank[$i])) {
                        $data['rank'] = (int)$mapRank[$i];
                    } 

                    if(isset($mapStartrank[$i])){
                        $data['start_rank'] = (int)$mapStartrank[$i];
                        $data['end_rank'] = (int)$mapEndRank[$i];
                    }
                    $event->event_map_time_delay()->create($data);
                }
            }*/
            
            /*END MAP TIME DELAY*/
        /* END HUNT AND PRIZE */
        return response()->json([
            'status'  => true,
            'message' => 'Event has been updated successfully.',
            'id'      => $event->id
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
        $event = Event::where('_id', $id)->first();
        $event->prizes()->delete();
        // $event->event_map_time_delay()->delete();
        $event->delete();
        return response()->json([
            'status' => true,
            'message'=>'Event has been deleted successfully.',
        ]);
    }


    public function getEventList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $events = Event::select('name','type','coin_type','starts_at','ends_at','rejection_ratio','winning_ratio','city_id','fees','starts_at','ends_at','event_days');
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
        ->editColumn('coin_type',function($event){
            return strtoupper($event->coin_type);
        })
        ->addColumn('city',function($event){
            return $event->city->name;
        })
        ->addColumn('starts_at',function($event){
            return $event->starts_at->format('d-m-Y');
        })
        ->addColumn('ends_at',function($event){
            return $event->ends_at->format('d-m-Y');
        })
        ->addColumn('action', function($query) use ($admin){
            $data = '';
                //$data .=  '<a href="'.route('admin.event.basicDetails',$query->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            if($admin->hasPermissionTo('Edit Event')){
                $data .=  '<a href="'.route('admin.event.show',$query->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
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



    //GET HUNT DETAILS
    public function getHuntList(Request $request){
        $cityId = $request->get('id');
        // $event = Event::where('city_id',$cityId)->with('city:_id,name')->first();
        $city = City::where('_id',$cityId)->first();
        $hunts = Hunt::select('name','place_name','city')
                ->where('city',$city->name)
                ->get();

        if (count($hunts) > 0) {
            return response()->json([
                'status'  => true,
                'message' => 'Get data successfully',
                'data' => $hunts,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Hunt city not found please try again',
            ]);
        }
    }

}