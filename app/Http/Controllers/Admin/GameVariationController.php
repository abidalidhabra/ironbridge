<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use App\Models\v1\GameVariation;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use File;
use Image;
use stdClass;
use Storage;

class GameVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.variations.variation_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $games = Game::where('status',true)
                        ->get();
        return view('admin.variations.create_variation',compact('games'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $identifier = $request->get('identifier');
        $rules = [];
        $messages = [];
        if ($identifier == 'jigsaw') {
            $rules = [
                        'variationSize' => 'required|in:12,35,70,140',   
                        'variation_image.*' => 'required|mimes:jpeg,jpg,png|dimensions:width=2000,height=1440',                      
                    ];
        }
        if ($identifier == 'block') {
            $rules = [
                        'row'    => 'required|in:9,10',   
                        'column' => 'required|in:9,10|same:row',   
                    ];
        }

        if ($identifier == '2048') {
            $rules = [
                        'row'    => 'required|in:4,6,8',   
                        'column' => 'required|in:4,6,8|same:row',   
                        'target' => 'required|in:1024,2048,4096',   
                    ];
        }

        $validator = Validator::make($request->all(),$rules);
        /*$validator = Validator::make($request->all(),[
                        'identifier' => "required",
                        'variationSize' => 'required_if:list_type,jigsaw',
                        // 'variation_image' => 'required_if:list_type,jigsaw|mimes:jpeg,bmp,png',
                    ]);*/

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        try {

            // $variationID        = $this->getNextID('games_variations');
            $sudoku_id          = (int)$request->get('sudoku_id');
            $gameID             = $request->get('game_id');
            $identifier         = $request->get('identifier');
            $variationSize      = (int)$request->get('variationSize');  
            $row                = ($request->get('row') == "")?0:(int)$request->get('row');
            $column             = ($request->get('column') == "")?0:(int)$request->get('column');
            $variationAlias     = $request->get('variation_name');  
            $variationComplexity= $request->get('variationComplexity');
            $pathOfImageTobeSave = storage_path('app/public/game_variations');
            $number_generate     = ($request->get('number_generate') == '')?0:(int)$request->get('number_generate');
            $target     =($request->get('target') == "")?0:(int)$request->get('target');
            $no_of_balls     =($request->get('no_of_balls') == "")?0:(int)$request->get('no_of_balls');
            $bubble_level_id     =($request->get('bubble_level_id') == "")?0:(int)$request->get('bubble_level_id');

            if(!File::exists($pathOfImageTobeSave)){
                File::makeDirectory($pathOfImageTobeSave,0755,true);
            }

            $imageUniqueName = new stdClass();
            if ($identifier == 'sliding' || $identifier == 'slidingphoto' || $identifier == 'jigsaw') {
                $variationImages     = $request->file('variation_image');
                foreach ($variationImages as $key => $variationImage) {
                    $extension = $variationImage->getClientOriginalExtension();
                    $img = Image::make($variationImage);
                    $jsonIndex = $key+1;
                    $imageUniqueName->$jsonIndex = uniqid('variation_'.uniqid(true).'_').'.'.$extension;
                    $img->save($pathOfImageTobeSave.'/'.$imageUniqueName->$jsonIndex);
                }
            }

            $data = [
                        'game_id'              => $gameID,
                        'variation_name'       => $variationAlias,
                        'variation_size'       => $variationSize,
                        'variation_complexity' => $variationComplexity,
                        'variation_image'      => $imageUniqueName,
                        'sudoku_id'            => $sudoku_id,
                        'row'                  => $row,
                        'target'               => $target,
                        'column'               => $column,
                        'number_generate'      => $number_generate,
                        'no_of_balls'          => $no_of_balls,
                        'bubble_level_id'      => $bubble_level_id,
                    ];

            
            GameVariation::create($data);

            return response()->json(['status'=>true,'message'=>'Add game variation has been created successfully']);
              
        } catch (Exception $e) {
            return response()->json(['status'=>false,'message'=>'Please try again']);   
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $variations = GameVariation::with('game')->where('_id',$id)->first();
        $games = Game::get();
        $variationsImage = \DB::table('game_variations')->where('_id',$id)->first();
        //$variations->variation_image1 = $variationsImage['variation_image'];
        
        return view('admin.variations.edit_variation',compact('games','variations'));
        
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
        $identifier = $request->get('identifier');
        $rules = [];
        if ($identifier == 'jigsaw') {
            $rules = [
                        'variationSize' => 'required|in:12,35,70,140',   
                        'variation_image.*' => 'mimes:jpeg,jpg,png|dimensions:width=2000,height=1440',                      
                    ];
        }
        if ($identifier == 'block') {
            $rules = [
                        'row'    => 'required|in:9,10',   
                        'column' => 'required|in:9,10|same:row',   
                    ];
        }

        if ($identifier == '2048') {
            $rules = [
                        'row'    => 'required|in:4,6,8',   
                        'column' => 'required|in:4,6,8|same:row',   
                        'target' => 'required|in:1024,2048,4096',   
                    ];
        }

        $validator = Validator::make($request->all(),$rules);
        /*$validator = Validator::make($request->all(),[
                        'identifier' => "required",
                        'variationSize' => 'required_if:list_type,jigsaw',
                        // 'variation_image' => 'required_if:list_type,jigsaw|mimes:jpeg,bmp,png',
                    ]);*/
        if ($validator->fails()) {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        try {
            //$gameVariation = GameVariation::where('_id',$id)->first();
            $gameVariation = GameVariation::where('_id',$id)->first();
            $variation_image = [];
           
            foreach ($gameVariation->variation_image as $key => $value) {
                $variation_image[] = substr(strrchr($value,'/'),1);
            }
            

            //$oldImages = $gameVariation['variation_image'];

            $gameId                 = $request->get('game_id');  
            $variation_name         = $request->get('variation_name');  
            $variationSize          = (int)$request->get('variationSize');  
            $variationComplexity    = $request->get('variationComplexity'); 
            $gameName               = $request->get('gameName');
            $identifier             = $request->get('identifier');
            $old_variationImage     = $request->get('old_variation_image');
            $sudoku_id              = (int)$request->get('sudoku_id');
            $row                    = ($request->get('row') == "")?null:(int)$request->get('row');
            $column                 = ($request->get('column') == "")?null:(int)$request->get('column');
            $target                 = ($request->get('target') == "")?null:(int)$request->get('target');
            $no_of_balls            = ($request->get('no_of_balls') == "")?null:(int)$request->get('no_of_balls');
            $bubble_level_id        = ($request->get('bubble_level_id') == "")?null:(int)$request->get('bubble_level_id');
            $number_generate        = ($request->get('number_generate') == '')?null:(int)$request->get('number_generate');

            
            //upload images
            $pathOfImageTobeSave = storage_path('app/public/game_variations');
            $image_path = $pathOfImageTobeSave;
            // File::delete($image_path);
            if(!File::exists($pathOfImageTobeSave)){
                File::makeDirectory($pathOfImageTobeSave,0755,true);
            }
            // print_r($oldImages);
            // print_r($request->get('old_variation_image'));
            // print_r($gameVariation['variation_image']);
            // exit();

            $imageUniqueName = new stdClass();
            if ($identifier == 'sliding' || $identifier == 'slidingphoto' || $identifier == 'jigsaw') {
                $variationImages     = $request->file('variation_image');
                $oldImages = [];
                $sameImage = [];
                if ($request->get('old_variation_image')) {
                    $oldImages = array_diff($variation_image, $request->get('old_variation_image'));
                    $sameImage = array_intersect($variation_image, $request->get('old_variation_image'));
                }
                
                if ($request->hasFile('variation_image') && $request->hasFile('variation_image')!="") {
                    foreach ($variationImages as $key => $variationImage) {
                        $extension = $variationImage->getClientOriginalExtension();
                        $img = Image::make($variationImage);
                        $jsonIndex = $key+1;
                        $imageUniqueName->$jsonIndex = uniqid('variation_'.uniqid(true).'_').'.'.$extension;
                        $img->save($pathOfImageTobeSave.'/'.$imageUniqueName->$jsonIndex);
                    }

                    //removeold images
                    if ($oldImages) {
                        $variationImages     = $oldImages;
                        foreach ($variationImages as $key => $imageName) {
                            Storage::disk('public')->delete('game_variations/'.$imageName);
                        }
                    }
                    // $data['variation_image'][] = $imageUniqueName;
                    $gameVariation->unset('variation_image');
                    $imageUniqueName = (object)array_merge($sameImage,(array)$imageUniqueName);
                } else {

                    $variationImages     = $variation_image;
                    $imageUniqueName = $variationImages;
                }
            }
            /*print_r($oldImages);
            exit;*/

            //insert data
            $data = array(
                'variation_name' => $variation_name,
                'variation_size' => $variationSize,
                'variation_complexity' => $variationComplexity,
                'variation_image' => $imageUniqueName,
                'sudoku_id' => (int)$sudoku_id,
                'row' => $row,
                'column' => $column,
                'target' => $target,
                'no_of_balls' => $no_of_balls,
                'bubble_level_id' => $bubble_level_id,
            );
            // $gameVariation->update($data);
            GameVariation::where('_id',$id)->update($data);
            return response()->json(['status'=>true,'message'=>'Game variation has been updated successfully']);
        } catch (Exception $e) {
            return response()->json(['status'=>false,'message'=>'Please try again']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        GameVariation::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'Game variation has been successfully deleted',
        ]);
    }

    public function getGameVariationList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $count = GameVariation::get()->count();
        return DataTables::of(GameVariation::with('game:_id,name')->orderBy('created_at','DESC')->skip($skip)->take($take)->get())
        ->addIndexColumn()
        ->editColumn('game_name', function($query){
            return $query->game->name;
        })
        ->editColumn('variation_complexity', function($query){
            return ucfirst($query->variation_complexity);
        })
        ->editColumn('variation_image', function($query){
            if(is_array($query->variation_image) && !empty($query->variation_image)){
                $images = '';
                foreach($query->variation_image as $key => $image){
                    
                    $images .= '<a data-fancybox="gallery" href="'.$image.'"><img width="50" height="auto" src="'.$image.'"></a>';
                }
                return $images;
            }
            return '';
        })
        ->editColumn('variation_size', function($query){
            if ($query->variation_size) {
                return $query->variation_size;
            } else if($query->row != 0 && $query->column != 0) {
                return $query->row.' * '.$query->column;
            } else {
                return '-';
            }
        })
        ->addColumn('action', function($query){
            return '<a href="'.route('admin.gameVariation.show',$query->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
            <a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$query->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
            </a>';
        })
        ->rawColumns(['action','variation_image'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
        ->skipPaging()
        ->make(true);
    }

    //DELETE IMAGE
    public function deleteImage(Request $request){
        $id = $request->get('id');
        $index = $request->get('index');

        //$gameVariation = GameVariation::where('_id',$id)->first();
        $gameVariation = \DB::table('game_variations')->where('_id',$id)->first();
        
        echo "<pre>";
        print_r($gameVariation['variation_image'][1]);
        exit();
        GameVariation::where('_id',$id)->pull('variation_image', $gameVariation->variation_image[0]);
        print_r($id);
        print_r($index);
        print_r($gameVariation->variation_image);
        exit();

    }
}
