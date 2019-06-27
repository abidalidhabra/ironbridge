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
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();
		Game::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'Add game has been created successfully',
        ]);
    }


    //GAME LIST
    public function getGameList(Request $request){
        return DataTables::of(Game::orderBy('created_at','DESC')->get())
        ->addIndexColumn()
        ->addColumn('action', function($game){
            // return '<a href="javascript:void(0)" class="edit_game" data-action="edit" data-id="'.$game->id.'" data-identifier="'.$game->identifier.'" data-name="'.$game->name.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
            // <a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$game->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
            // </a>';
            return '<a href="javascript:void(0)" class="edit_game" data-action="edit" data-id="'.$game->id.'" data-identifier="'.$game->identifier.'" data-name="'.$game->name.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
                <a href="javascript:void(0)" class="delete_game" data-action="delete" data-placement="left" data-id="'.$game->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>';
        })
        ->rawColumns(['action'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->make(true);
    }

    //EDIT GAME
    public function editGame(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        'identifier' => 'required',
                        'name' 		 => 'required',
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
											]);
		
		return response()->json([
            'status' => true,
            'message'=>'Updated game has been created successfully',
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
            'message'=>'Game has been successfully deleted',
        ]);
    }
}
