<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\Models\v1\Game;
use App\Models\v2\TreasureNodesTarget;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;

class TreasureNodesTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::where('status',true)->get();

        return view('admin.treasure_nodes_targets',compact('games'));
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
        //
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
        $validator = Validator::make($request->all(), [
            'score' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(), 'status' => false]);
        }

        $treasureNodesTarget = TreasureNodesTarget::where('_id',$id)->first();
        $treasureNodesTarget->score = (int)$request->score;
        $treasureNodesTarget->save();
        
        return response()->json([
            'status' => true,
            'message'=>'Minigames nodes has been update successfully.',
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

    public function getTreasureNodesTargetsList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $treasureNodesTargets = TreasureNodesTarget::when($search != '', function($query) use ($search) {
            $query->where('_id','game_id','%'.$search.'%')
            ->orWhere('score','like','%'.$search.'%');
        })
        ->orderBy('created_at','DESC')
        ->skip($skip)
        ->take($take)
        ->get();
        
        /** Filter Result Total Count  **/
        $filterCount = TreasureNodesTarget::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('score','like','%'.$search.'%');
        })
        ->count();
                
        $admin = Auth::User();
        return DataTables::of($treasureNodesTargets)
        ->addIndexColumn()
        ->addColumn('game', function($treasureNodesTarget){
            return $treasureNodesTarget->game->name;
        })
        ->addColumn('action', function($treasureNodesTarget) use($admin){
                $html = '';
                $html .= '<a href="javascript:void(0)" class="edit_treasureNodesTarge" data-id="'.$treasureNodesTarget->id.'" data-score="'.$treasureNodesTarget->score.'" data-game="'.$treasureNodesTarget->game_id.'"  data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
                
            return $html;
        })
        ->rawColumns(['action'])
        ->setTotalRecords(TreasureNodesTarget::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
