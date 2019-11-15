<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v2\AgentComplementary;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Auth;



class HuntAgentLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agent_complementary = AgentComplementary::get();
        return view('admin.agent-levels.hunts.index',compact('agent_complementary'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agent_complementary = AgentComplementary::get();

        return view('admin.agent-levels.hunts.create',compact('agent_complementary'));
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
            'agent_level' => 'required|numeric',
            'complexity'  => 'required|numeric|min:1',
        ],[
            'complexity.required'  => 'The difficulty field is required.'
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        
        $data = $request->all();

        $agentComplementary = AgentComplementary::where('agent_level',(int)$request['agent_level'])->first();
        $agentComplementary->complexity = (int)$request['complexity'];
        $agentComplementary->save();
        
        return response()->json(['status' => true,'message' => 'Hunts agent levels has been added successfully']);
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
       
       return view('admin.agent-levels.hunts.edit',compact('agent_complementary','agent_data'));
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
        $validator = Validator::make($request->all(),[
            'agent_level' => 'required|numeric',
            'complexity'  => 'required|numeric|min:1',
        ],[
            'complexity.required'  => 'The difficulty field is required.'
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $agent_complementary = AgentComplementary::where('_id',$id)->first();
        $agent_complementary->agent_level = (int)$request['agent_level'];
        $agent_complementary->complexity = (int)$request['complexity'];
        $agent_complementary->save();

        return response()->json(['status' => true,'message' => 'Hunts agent levels has been updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AgentComplementary::where('_id',$id)->unset('complexity');
        return response()->json(['status'=> true, 'message'=> 'Hunts agent levels has been deleted successfully.']);
    }

    
    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $agent_complementary = AgentComplementary::whereNotNull('complexity')
        ->when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('agent_level','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        ->orderBy('agent_level','ASC')
        ->skip($skip)
        ->take($take)
        ->get();
        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::whereNotNull('complexity')
        ->when($search != '', function($query) use ($search) {
            $query->where('agent_level','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        ->count();
        
        $admin = Auth::User();
        return DataTables::of($agent_complementary)
        ->addIndexColumn()
        ->editColumn('complexity', function($relic){
            return ($relic->complexity)?$relic->complexity:'-';
        })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('action', function($relic) use ($admin){
                $html = '';
                    $html .= '<a href="javascript:void(0)" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox edit_agent" data-id="'.$relic->id.'"></i></a>';

                    //$html .= ' <a href="'.route('admin.hunts-agent-levels.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                
                //$html .= ' <a href="'.route('admin.hunts-agent-levels.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(AgentComplementary::whereNotNull('complexity')->count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
