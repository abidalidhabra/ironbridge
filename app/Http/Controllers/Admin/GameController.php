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
        $count = Game::count();
        return DataTables::of(Game::orderBy('created_at','DESC')->skip($skip)->take($take)->get())
        ->addIndexColumn()
        ->addColumn('action', function($game){
            return '<a href="javascript:void(0)" class="edit_game" data-action="edit" data-id="'.$game->id.'" data-identifier="'.$game->identifier.'" data-name="'.$game->name.'" data-status="'.$game->status.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';

            /*return '<a href="javascript:void(0)" class="edit_game" data-action="edit" data-id="'.$game->id.'" data-identifier="'.$game->identifier.'" data-name="'.$game->name.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>
            <a href="javascript:void(0)" class="delete_game" data-action="delete" data-placement="left" data-id="'.$game->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>';*/
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
}
