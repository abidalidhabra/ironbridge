<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\PracticeGameTarget;
use File;
use Image;
use stdClass;
use Storage;
use Auth;
use App\Models\v1\HuntClue;

class GameController extends Controller
{
    public function index()
    {
    	return view('admin.game.index');
    }

    //ADD GAME
    public function addgame(Request $request){
    	$validator = Validator::make($request->all(),[
            'identifier' => 'required',
            'name' 		 => 'required',
            'status'     => 'required|in:active,inactive',
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();
        $data['status'] = ($data['status'] == 'active')?true:false;
        Game::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'Game has been added successfully.',
        ]);
    }


    //GAME LIST
    public function getGameList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $game = Game::select('identifier','name','status');
        $admin = Auth::user();

        if($search != ''){
            $game->where(function($query) use ($search){
                $query->where('identifier','like','%'.$search.'%')
                ->orWhere('name','like','%'.$search.'%')
                ->orWhere('status','like','%'.$search.'%');
            });
        }
        $game = $game->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = Game::count();
        if($search != ''){
            $count = Game::where(function($query) use ($search){
                $query->where('identifier','like','%'.$search.'%')
                ->orWhere('name','like','%'.$search.'%')
                ->orWhere('status','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($game)
        ->addIndexColumn()
        ->addColumn('action', function($game) use ($admin){
            if($admin->hasPermissionTo('Edit Games')){

                return '<a href="javascript:void(0)" class="edit_game" data-action="edit" data-id="'.$game->id.'" data-identifier="'.$game->identifier.'" data-name="'.$game->name.'" data-status="'.$game->status.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }
            return '';
        })
        ->editColumn('status', function($game){
            if ($game->status) {
                return 'Active';
            } else {
                return 'Inactive';
            }
        })
        ->rawColumns(['action'])
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

    //EDIT GAME
    public function editGame(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'identifier' => 'required',
            'name' 		 => 'required',
            'status'     => 'required|in:active,inactive',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $gameId = $request->get('game_id');

        Game::where('_id',$gameId)->update([
            'identifier' => $request->get('identifier'),
            'name' => $request->get('name'),
            'status' => ($request->get('status')== 'active')?true:false,
        ]);

        /* REPLACE GAME IN HUNT CLUE */
        if ($request->get('status') == 'inactive') {
            $allGame = Game::where('status',true)
                            ->where('_id','!=',$gameId)
                            ->with('game_variation:_id,game_id')
                            ->get();
            
            $huntClue = HuntClue::select('_id' , 'game_id', 'game_variation_id' ,'hunt_complexity_id')
                                ->get()
                                ->groupBy('hunt_complexity_id')
                                ;

            // $gameHuntClude =  $huntClue->where('game_id',$gameId)->groupBy('hunt_complexity_id');
            /*foreach ($gameHuntClude as $key => $value) {
                $usedGameId = [];
                $usedGameId = $value->pluck('game_id')->toArray();
                if (in_array($gameId, $usedGameId)) {
                    $huntCludeGameId = $huntClue->where('hunt_complexity_id',$key)->pluck('game_id')->toArray();
                    $usedGameId = array_merge($usedGameId, $huntCludeGameId);
                    foreach ($value as $game) {
                        if (in_array($gameId, $usedGameId)) {
                            $newGame = $allGame->whereNotIn('_id',$usedGameId)->random(1)->first();
                            array_push($usedGameId, $newGame->id);
                            $game->game_id = $newGame->id; 
                            $game->game_variation_id = $newGame->game_variation->random(1)->first()->id; 
                            $game->save();
                        }
                    }
                }
            }*/
            foreach ($huntClue as $key => $value) {
                $usedGameId = [];
                $usedGameId = $value->pluck('game_id')->toArray();
                
                if (in_array($gameId, $usedGameId)) {
                    foreach ($value as $game) {
                        if ($gameId == $game->game_id) {
                            if (in_array($game->game_id , $usedGameId)) {
                                $newGame = $allGame->whereNotIn('_id',$usedGameId)->random(1)->first();
                                // if (isset($newGame) && !empty($newGame->toArray)) {
                                $game->game_id = $newGame->id; 
                                $game->game_variation_id = $newGame->game_variation->random(1)->first()->id; 
                                //$game->game_variation_id = $newGame->game_variation->random()->first()->id; 
                                $game->save();
                                array_push($usedGameId, $newGame->id);
                            }
                        }
                    }
                }
            }
        }
        /* END REPLACE GAME IN HUNT CLUE */

        return response()->json([
            'status' => true,
            'message'=>'Game has been updated successfully.',
        ]);
    }

    //DELETE GAME
    public function deleteGame(Request $request){
        $id = $request->get('id');
        $game = Game::where('_id', $id)->first();
        $game->game_variation()->delete();
        $game->delete();
        return response()->json([
            'status' => true,
            'message'=>'Game has been deleted successfully.',
        ]);
    }

    /* practice games target */
    public function practiceGame(Request $request){
        $practiceGames = PracticeGameTarget::with('game:_id,name,status')
                                            ->get();
        $moregame = PracticeGameTarget::with('game:_id,name,status')
                                        ->whereIn('game_id',['5b0e306951b2010ec820fb4f','5b0e304b51b2010ec820fb4e'])
                                        ->get();

        return view('admin.game.practice_games',compact('practiceGames','moregame'));
    }    

    /*  */
    public function gameTargetUpdate(Request $request){
        $gameId = $request->get('game_id');
        
        if ($gameId == '5c188ab5719a1408746c473b') {
            $rules = [
                        'target' => 'required|in:512,1024,2048,4096',
                    ];
        } elseif ($gameId == '5b0e304b51b2010ec820fb4e') {
            $rules = [
                        'target' => 'required|in:12,35,70,140',
                    ];
        } elseif ($gameId == '5b0e306951b2010ec820fb4f') {
            $rules = [
                        'target' => 'required|in:4,5,6',
                    ];
        } elseif ($gameId == '5b0e2ff151b2010ec820fb48') {
            $rules = [
                        'target' => 'required|integer|between:1,81',
                    ];
        } else {
            $rules = [
                        'target' => 'required',
                    ];
        }

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $id     = $request->get('id');
        $target = $request->get('target');

        /*if($gameId == '5b0e2ff151b2010ec820fb48'){
            $data['variation_size']=(int)$target;
        } elseif($gameId == '5b0e303f51b2010ec820fb4d'){
            $data['number_generate']=(int)$target;
        } else {*/
            $data['target']=(int)$target;
        //}

        PracticeGameTarget::where('_id',$id)
                            ->update($data);

        return response()->json([
            'status' => true,
            'message'=>'Target has been updated successfully.',
        ]);
    }

    public function variationSizeUpdate(Request $request){
        $gameId = $request->get('game_id');
        
        $rules = [];
        if ($gameId == '5b0e304b51b2010ec820fb4e') {
            $rules = [
                //'variation_size' => 'required|in:12,35,70,140',   
                // 'variation_image.*' => 'mimes:jpeg,jpg,png|dimensions:width=2000,height:1440,1400',                      
                'variation_image.*' => 'mimes:jpeg,jpg,png|dimensions:width=1440,height:2000',
            ];
        }

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $id = $request->get('practice_game');
        $pathOfImageTobeSave = storage_path('app/public/practice_games');
        $variation_image = [];
        $practiceGame = PracticeGameTarget::where('_id',$id)->first();

        if(!File::exists($pathOfImageTobeSave)){
            File::makeDirectory($pathOfImageTobeSave,0755,true);
        }

        $imageUniqueName = new stdClass();
        
        if ($request->hasFile('variation_image') && $request->hasFile('variation_image')!="") {
            $variationImages     = $request->file('variation_image');
            foreach ($variationImages as $key => $variationImage) {
                $extension = $variationImage->getClientOriginalExtension();
                $img = Image::make($variationImage);
                $jsonIndex = $key+1;
                $imageUniqueName->$jsonIndex = uniqid('practice_'.uniqid(true).'_').'.'.$extension;
                $img->save($pathOfImageTobeSave.'/'.$imageUniqueName->$jsonIndex);
                

                $imageNmae[] = (array)$imageUniqueName;
                $practiceGame->push('variation_images', $imageUniqueName->$jsonIndex,true);
            }

        }

        return response()->json([
            'status' => true,
            'message'=>'Practice games has been updated successfully.',
        ]);
    }

    public function variationSizeUpdate_old(Request $request){
        $gameId = $request->get('game_id');
        
        $rules = [];
        if ($gameId == '5b0e304b51b2010ec820fb4e') {
            $rules = [
                //'variation_size' => 'required|in:12,35,70,140',   
                // 'variation_image.*' => 'mimes:jpeg,jpg,png|dimensions:width=2000,height=1440',
                'variation_image.*' => 'mimes:jpeg,jpg,png|dimensions:width=738,height:1024',
            ];
        }

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $id = $request->get('practice_game');
        $pathOfImageTobeSave = storage_path('app/public/practice_games');
        $variation_image = [];
        $practiceGame = PracticeGameTarget::where('_id',$id)->first();
        if ($practiceGame->variation_image) {
            foreach ($practiceGame->variation_image as $key => $value) {
                $variation_image[] = substr(strrchr($value,'/'),1);
                $oldImage = substr(strrchr($value,'/'),1); 
                /*if ($gameId != '5b0e304b51b2010ec820fb4e') {
                    if ($request->hasFile('variation_image') && $request->hasFile('variation_image')!=""){
                        Storage::disk('public')->delete('practice_games/'.$oldImage);
                    }
                }*/
            }
        }


        if(!File::exists($pathOfImageTobeSave)){
            File::makeDirectory($pathOfImageTobeSave,0755,true);
        }

        $imageUniqueName = new stdClass();
        
        if ($request->hasFile('variation_image') && $request->hasFile('variation_image')!="") {
            $variationImages     = $request->file('variation_image');
            foreach ($variationImages as $key => $variationImage) {
                $extension = $variationImage->getClientOriginalExtension();
                $img = Image::make($variationImage);
                $jsonIndex = $key+1;
                $imageUniqueName->$jsonIndex = uniqid('practice_'.uniqid(true).'_').'.'.$extension;
                $img->save($pathOfImageTobeSave.'/'.$imageUniqueName->$jsonIndex);
            }
            //if ($gameId == '5b0e304b51b2010ec820fb4e') {
                $imageUniqueName = (object) array_merge($variation_image, (array) $imageUniqueName); 
            //    $practiceGame['variation_image'] = $imageUniqueName;
            // /}
        }/*  else {
            $variationImages = $variation_image;
            $imageUniqueName = (object)$variationImages;
        }*/

        
        $practiceGame['variation_image'] = $imageUniqueName;
        //$practiceGame['variation_size'] = (int)$request->get('variation_size');
        $practiceGame->save();
        
        return response()->json([
            'status' => true,
            'message'=>'Practice games has been updated successfully.',
        ]);
    }


    public function practiceDeleteImage(Request $request){
        $id = $request->get('id');
        $image = substr(strrchr($request->get('image'),'/'),1);

        $practiceGame = PracticeGameTarget::where('_id',$id)->first();   
        /*$variation_image = [];
        if ($practiceGame->variation_images) {
            foreach ($practiceGame->variation_images as $key => $value) {
                $convert_image = substr(strrchr($value,'/'),1);  
                if ($image != $convert_image) {
                    $variation_image[] = $convert_image;  
                } else {
                }
            }
        }*/

        
        $practiceGame->pull('variation_images', $image);
        Storage::disk('public')->delete('practice_games/'.$image);
        //$practiceGame->variation_image = (object)$variation_image;
        //$practiceGame->save();

        return response()->json([
            'status' => true,
            'message'=>'image has been deleted successfully',
        ]);
    }
}
