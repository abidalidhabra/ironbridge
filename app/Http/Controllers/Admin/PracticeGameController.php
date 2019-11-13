<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Models\v1\PracticeGameTarget;
use App\Models\v1\Game;
use Auth,Validator,File,stdClass,Image;


class PracticeGameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.game.practice.index');
    }

    /**
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $practiceGame = PracticeGameTarget::where('_id',$id)
                                            ->with('game')
                                            ->first();
        $games = Game::where('status',true)->get();
        
        return view('admin.game.practice.show',compact('practiceGame','games'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $practiceGame = PracticeGameTarget::find($id);
        $games = Game::where('status',true)->get();
        
        return view('admin.game.practice.edit',compact('practiceGame','games'));
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
        $validator = Validator::make($request->all(), [
            'score.*'=> 'required_without:time.*',
            'time.*'=> 'required_without:score.*',
            'xp.*'=> 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(), 'status'=>false]);
        }

        $target = [];
        for ($i=0; $i < count($request->xp) ; $i++) { 
            $target[$i]['stage'] = $i+1;
            if (isset($request['score'][$i]) && $request['score'][$i] != "") {
                $target[$i]['score'] = (int)$request['score'][$i];
            }
            if (isset($request['time'][$i]) && $request['time'][$i] != "") {
                $target[$i]['time'] = (int)$request['time'][$i];
            }
            if (isset($request['xp'][$i]) && $request['xp'][$i] != "") {
                $target[$i]['xp'] = (int)$request['xp'][$i];
            }
        }


        $practiceGame = PracticeGameTarget::where('_id',$id)->first();
        
        $practiceGame->targets = $target;

        /* VARIATION IMAGE */
        $pathOfImageTobeSave = storage_path('app/public/practice_games');
        $variation_image = [];

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
        /* END VARIATION IMAGE */

        $practiceGame->save();
        return response()->json([
            'status' => true,
            'message'=>'Practice Game has been updated successfully.',
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
        //
    }


    public function GetPracticeGameList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $practiceGames = PracticeGameTarget::select('game_id', 'targets','variation_images')
                                            ->with('game');
        $admin = Auth::user();

        if($search != ''){
            $practiceGames->where(function($query) use ($search){
                $query->where('game_id','like','%'.$search.'%')
                ->orWhere('targets','like','%'.$search.'%')
                ->orWhere('variation_images','like','%'.$search.'%');
            });
        }
        $practiceGames = $practiceGames->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = PracticeGameTarget::count();
        if($search != ''){
            $count = PracticeGameTarget::where(function($query) use ($search){
                $query->where('game_id','like','%'.$search.'%')
                ->orWhere('targets','like','%'.$search.'%')
                ->orWhere('variation_images','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($practiceGames)
        ->addIndexColumn()
        ->addColumn('game', function($practiceGame) use ($admin){
            return $practiceGame->game->name;
        })
        ->addColumn('targets', function($practiceGame) use ($admin){
            return count($practiceGame->targets); 
        })
        ->addColumn('action', function($practiceGame) use ($admin){
            $html = '<a href="'.route('admin.practiceGame.edit',$practiceGame->id).'" class="edit_game" data-action="edit" data-id="'.$practiceGame->id.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            
            $html .= ' <a href="'.route('admin.practiceGame.show',$practiceGame->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';

            return $html;
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

    public function targerHTML(Request $request){
        if($request->ajax()) {
            return view('admin.game.practice.targets.create')->with(['index'=> $request->index]);
        }
        throw new Exception("You are not allow make this request");
    }
}
