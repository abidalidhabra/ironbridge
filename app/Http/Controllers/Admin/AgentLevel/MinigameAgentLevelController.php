<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\v2\AgentComplementary;
use App\Models\v1\Game;
use Yajra\DataTables\DataTables;
use Auth;

class MinigameAgentLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::where('status',true)
                        ->get();
        $agent_complementary = AgentComplementary::get();

        return view('admin.agent-levels.minigames.index',compact('games','agent_complementary'));
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
        $data = $request->all();
        if (!isset($request['minigames'])) {
            $data['minigames'] = "";
        }

        $validator = Validator::make($data,[
            'agent_level' => 'required|numeric',
            'minigames'  => 'required',
            'minigames.*'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $agent_complementary = AgentComplementary::where('agent_level',(int)$request['agent_level'])->first();
        
        $agent_complementary->agent_level = (int)$request['agent_level'];
        $agent_complementary->minigames = $request['minigames'];
        $agent_complementary->save();
        

        return response()->json(['status' => true,'message' => 'Minigames Agent levels has been added successfully']);
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
       $agent_complementary = AgentComplementary::get();
       $agent_data = AgentComplementary::where('_id',$id)->first();
       $games = Game::where('status',true)
                        ->get();
       return view('admin.agent-levels.minigames.edit',compact('agent_complementary','agent_data','games'));
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
        $data = $request->all();
        if (!isset($request['minigames'])) {
            $data['minigames'] = "";
        }

        $validator = Validator::make($data,[
            'agent_level' => 'required|numeric',
            'minigames'  => 'required',
            'minigames.*'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }
        $agent_complementary = AgentComplementary::where('_id',$id)->first();
        $agent_complementary->agent_level = (int)$request['agent_level'];
        $agent_complementary->minigames = $request['minigames'];
        $agent_complementary->save();

        return response()->json(['status' => true,'message' => 'Minigames Agent levels has been updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AgentComplementary::where('_id',$id)->unset('minigames');

        return response()->json(['status'=> true, 'message'=> 'Agent Levels has been deleted successfully.']);
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $agent_complementary = AgentComplementary::whereNotNull('minigames')
        ->when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('agent_level','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        
        ->orderBy('created_at','DESC')
        ->skip($skip)
        ->take($take)
        ->get();
        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::whereNotNull('minigames')
        ->when($search != '', function($query) use ($search) {
            $query->where('agent_level','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        ->count();
        
        $admin = Auth::User();
        return DataTables::of($agent_complementary)
        ->addIndexColumn()
        ->editColumn('minigames', function($relic){
            $games = Game::whereIn('_id',$relic->minigames)->get()->pluck('name')->toArray();
            return implode(',', $games);
        })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('action', function($relic) use ($admin){
                $html = '';
                if($admin->hasPermissionTo('Edit Agent Levels')){
                    $html .= '<a href="javascript:void(0)" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox edit_agent" data-id="'.$relic->id.'"></i></a>';
                }

                if($admin->hasPermissionTo('Delete Agent Levels')){
                    $html .= ' <a href="'.route('admin.minigames-agent-levels.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                }
                
                //$html .= ' <a href="'.route('admin.minigames-agent-levels.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(AgentComplementary::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
