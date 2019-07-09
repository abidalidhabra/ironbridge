<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v1\ComplexityTarget;
use App\Models\v1\Game;

class ComplexityTargetController extends Controller
{
    public function index()
    {
    	$games = Game::get();
    	return view('admin.complexityTarget.index',compact('games'));
    }

    public function getComplexityTarget(Request $request){
    	$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $count = ComplexityTarget::count();
        $complexityTarget = ComplexityTarget::with('game:_id,name')
        									->orderBy('created_at','DESC')
        									->skip($skip)
        									->take($take)
        									->get();
        return DataTables::of($complexityTarget)
        ->addIndexColumn()
        ->addColumn('action', function($complexity){
            return '<a href="javascript:void(0)" class="edit_complexity" data-action="edit" data-id="'.$complexity->id.'" data-complexity="'.$complexity->complexity.'" data-target="'.$complexity->target.'" data-game_id="'.$complexity->game_id.'"  data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
        })
        ->addColumn('game_name', function($complexity){
            return $complexity->game->name;
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


    //edit Complexity Target
    public function editComplexityTarget(Request $request){
    	 $validator = Validator::make($request->all(),[
                        'target' => 'required|integer',
                    ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $id = $request->get('id');

		ComplexityTarget::where('_id',$id)
						->update([
							'target' => $request->get('target'),
						]);
		
		return response()->json([
            'status' => true,
            'message'=>'Complexity target has been updated successfully',
        ]);
    }
}
