<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\v2\AgentComplementary;
use App\Models\v1\Game;
use Yajra\DataTables\DataTables;
use Auth;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agent_complementary = AgentComplementary::get();
        return view('admin.agent-levels.nodes.index',compact('agent_complementary'));
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
        if (!isset($request['nodes'])) {
            $data['nodes'] = "";
        }

        $validator = Validator::make($data,[
            'agent_level' => 'required|numeric',
            'nodes'       => 'required',
            'nodes.*'     => 'required',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }
        if ($request->power != "") {
            if ($request->power < 0 || $request->power > 100) {
                return response()->json(['status' => false,'message' => 'The power must be between 1 and 100.']);
            }
        }

        $agentComplementary = AgentComplementary::get()->pluck('nodes');
        foreach (array_filter($agentComplementary->toArray()) as $key => $value) {
            foreach ($request->nodes as $node) {
                if(in_array($node,array_keys($value))) {
                    if ($node == 'mg_challenge') {
                        $label = 'Mini-game Challenge Nodes';
                    } else if ($node == 'power') {
                        $label = 'Power Nodes';
                    } else if ($node == 'bonus') {
                        $label = 'Bonus Nodes';
                    }
                    return response()->json(['status' => false,'message' => 'Already Used '.$label]);
                }
            }
        }

        $nodes = [];
        foreach ($request->nodes as $key => $node) {
            if ($node == 'mg_challenge') {
               $nodes[$node] = true;
            } 
            if ($node == 'power') {
                $nodes[$node] = (object)['value' => 0];
                if (isset($request->power) && $request->power != "") {
                    $nodes[$node] = (object)['value'=> (int)$request->power];
                }
            }
            if ($node == 'bonus') {
               $nodes[$node] = true;
            }
        }
        $agentLevel = (int)$request['agent_level'];
        $agent_complementary = AgentComplementary::where('agent_level',$agentLevel)->first();
        
        if ($agent_complementary->nodes) {
            return response()->json(['status' => false,'message' => 'Agent level '.$agentLevel.' already used']);
        }
        $agent_complementary->agent_level = $agentLevel;
        $agent_complementary->nodes = (object)$nodes;
        $agent_complementary->save();
        

        return response()->json(['status' => true,'message' => 'Nodes Agent levels has been added successfully']);
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
       return view('admin.agent-levels.nodes.edit',compact('agent_complementary','agent_data','games'));
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
        

        $validator = Validator::make($data,[
            'agent_level' => 'required|numeric',
            'nodes'       => 'required',
            'nodes.*'     => 'required',
            // 'power'       => 'integer',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        if ($request->power != "") {
            if ($request->power < 0 || $request->power > 100) {
                return response()->json(['status' => false,'message' => 'The power must be between 1 and 100.']);
            }
        }

        $agentLevel = (int)$request['agent_level'];
        $agentComplementary = AgentComplementary::where('agent_level','!=',$agentLevel)
                                                ->where('_id','!=',$id)
                                                ->get()
                                                ->pluck('nodes');
        foreach (array_filter($agentComplementary->toArray()) as $key => $value) {
            foreach ($request->nodes as $node) {
                if(in_array($node,array_keys($value))) {
                    if ($node == 'mg_challenge') {
                        $label = 'Mini-game Challenge Nodes';
                    } else if ($node == 'power') {
                        $label = 'Power Nodes';
                    } else if ($node == 'bonus') {
                        $label = 'Bonus Nodes';
                    }
                    return response()->json(['status' => false,'message' => 'Already Used '.$label]);
                }
            }
        }
        $nodes = [];
        foreach ($request->nodes as $key => $node) {
            if ($node == 'mg_challenge') {
               $nodes[$node] = true;
            } 
            if ($node == 'power') {
                $nodes[$node] = (object)['value' => 0];
                if (isset($request->power) && $request->power != "") {
                    $nodes[$node] = (object)['value'=>(int)$request->power];
                }
            }
            if ($node == 'bonus') {
               $nodes[$node] = true;
            }
        }
        

        AgentComplementary::where('_id',$id)->first()->unset('nodes');

        $agent_complementary = AgentComplementary::where('agent_level',$agentLevel)->first();        
        /*if ($agent_complementary->nodes) {
            return response()->json(['status' => false,'message' => 'Agent level '.$agentLevel.' already used']);
        }*/
        $agent_complementary->agent_level = $agentLevel;
        $agent_complementary->nodes = (object)$nodes;
        $agent_complementary->save();
        

        return response()->json(['status' => true,'message' => 'Nodes Agent levels has been added successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AgentComplementary::where('_id',$id)->unset('nodes');
        return response()->json(['status'=> true, 'message'=> 'Nodes agent levels has been deleted successfully.']);
    }


    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $agent_complementary = AgentComplementary::whereNotNull('nodes')
        ->when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('agent_level','like','%'.$search.'%');
        })
        ->orderBy('agent_level','ASC')
        ->skip($skip)
        ->take($take)
        ->get();
        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::whereNotNull('nodes')
        ->when($search != '', function($query) use ($search) {
            $query->where('agent_level','like','%'.$search.'%');
        })
        ->count();
        
        $admin = Auth::User();
        return DataTables::of($agent_complementary)
        ->addIndexColumn()
        ->addColumn('special_ability', function($relic){
            $node = '';
            foreach ($relic->nodes as $key => $value) {
                if ($key == 'power') {
                    $node = 'Power Nodes ('. $value['value'].') , ';
                } elseif ($key == 'bonus') {
                    $node .= 'Bonus Nodes , ';
                } elseif ($key == 'mg_challenge') {
                    $node .= 'Mini-game Challenge Nodes , ';
                }
            }
            return substr($node, 0, -2);
        })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('action', function($relic) use ($admin){
                $html = '';
                if($admin->hasPermissionTo('Edit Bucket Size / Agent Levels')){
                    $html .= '<a href="javascript:void(0)" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox edit_agent" data-id="'.$relic->id.'"></i></a>';
                }
                $html .= ' <a href="'.route('admin.nodes-agent-levels.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                
                //$html .= ' <a href="'.route('admin.minigames-agent-levels.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(AgentComplementary::whereNotNull('nodes')->count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
