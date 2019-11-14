<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use App\Http\Controllers\Controller;
use App\Models\v1\User;
use App\Models\v2\AgentComplementary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class AgentLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.agent-levels.index');
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
        $request->validate([
            'agent_level'=> ['required', 'numeric', 'integer', 'min:1'],
            'xps'=> 'required|numeric|integer',
        ]);

        $alreadyExist = AgentComplementary::where('agent_level', (int)$request->agent_level)->count();
        if ($alreadyExist) {
            return response()->json(['status'=> false, 'message'=> 'Agent level already available, it must be unique.']);
        }else{
            return response()->json([
                'status'=> true,
                'message'=> 'Agent level added successfully.',
                'agent_complementary'=> AgentComplementary::create(['agent_level'=> (int)$request->agent_level, 'xps'=> (int)$request->xps])
            ]);
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
        return response()->json([
            'status'=> true,
            'agent_complementary'=> AgentComplementary::where('_id', $id)->first()
        ]);
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
        $request->validate([
            'agent_level'=> ['required', 'numeric', 'integer', 'min:1'],
            'xps'=> 'required|numeric|integer',
        ]);
        
        $alreadyExist = AgentComplementary::where('_id', '!=', $id)->where('agent_level', (int)$request->agent_level)->count();
        if ($alreadyExist) {
            return response()->json(['status'=> false, 'message'=> 'Agent level already available, it must be unique.'], 422);
        }else{
            return response()->json([
                'status'=> true,
                'message'=> 'Agent level updated successfully.',
                'agent_complementary'=> AgentComplementary::where('_id', $id)->update(['agent_level'=> (int)$request->agent_level, 'xps'=> (int)$request->xps])
            ]);
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
        $agentComplementary = AgentComplementary::find($id);
        $users = User::where('agent_status.level', '>=' ,$agentComplementary->agent_level)->count();
        if ($users > 0) {
            return response()->json(['status' => false, 'message'=>'This agent level cannot be delete.']);
        }else{
            $agentComplementary->delete();
            return response()->json(['status' => true, 'message'=>'Agent levels has been deleted successfully.']);
        }
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $agentComplementries = AgentComplementary::when($search != '', function($query) use ($search) {
                                    $active = ($search == 'true' || $search == 'Active')? true: false;
                                    $query->where('agent_level','like','%'.$search.'%')->orWhere('xps','like','%'.$search.'%');
                                })
                                ->orderBy('created_at','DESC')
                                ->skip($skip)
                                ->take($take)
                                ->get();

        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::when($search != '', function($query) use ($search) {
                            $query->where('agent_level','like','%'.$search.'%')->orWhere('xps','like','%'.$search.'%');
                        })
                        ->count();

        $admin = auth()->user();
        return DataTables::of($agentComplementries)
                ->addIndexColumn()
                ->addColumn('action', function($agentComplementary) use ($admin){
                        $html = '';
                        if($admin->hasPermissionTo('Edit Agent Levels')){
                            $html .= '<a href="javascript:void(0);" class="editAgentLevel"  title="Edit" data-edit-path="'.route('admin.agent-levels.edit', $agentComplementary->id).'"><i class="fa fa-pencil iconsetaddbox"></i></a>';
                        }

                        if($admin->hasPermissionTo('Delete Agent Levels')){
                            $html .= ' <a href="'.route('admin.agent-levels.destroy',$agentComplementary->id).'" data-id="'.$agentComplementary->id.'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                        }
                    return $html;
                })
                ->rawColumns(['action', 'icon'])
                ->setTotalRecords(AgentComplementary::count())
                ->setFilteredRecords($filterCount)
                ->skipPaging()
                ->make(true);
    }
}
