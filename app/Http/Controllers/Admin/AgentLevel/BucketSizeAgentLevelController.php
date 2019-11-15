<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use App\Http\Controllers\Controller;
use App\Models\v2\AgentComplementary;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BucketSizeAgentLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.agent-levels.hunt-bucket-size', ['levels'=> AgentComplementary::all()]);
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
            'bucket_size'=> 'required|numeric|integer|min:1',
        ]);

        $alreadyExist = AgentComplementary::where('agent_level', (int)$request->agent_level)
                                            ->where('bucket_size', (int)$request->bucket_size)
                                            ->count();
        if ($alreadyExist) {
            return response()->json(['status'=> false, 'message'=> 'Agent level already available, it must be unique.']);
        }else{
            return response()->json([
                'status'=> true,
                'message'=> 'Bucket Agent level added successfully.',
                'agent_complementary'=> AgentComplementary::where('agent_level',(int)$request->agent_level)
                                                            ->update(['bucket_size'=> (int)$request->bucket_size])
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
            'bucket_size'=> 'required|numeric|integer|min:1',
        ]);

        
        AgentComplementary::where('agent_level',(int)$id)
                            ->where('agent_level', (int)$request->agent_level)
                            ->update(['bucket_size' => (int)$request->bucket_size]);
        
        return response()->json([
            'status'=> true,
            'message'=> 'Agent level updated successfully.',
            //'agent_complementary'=> AgentComplementary::where('_id', $id)->update(['agent_level'=> (int)$request->agent_level, 'bucket_size'=> (int)$request->bucket_size])
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
        $agentComplementary = AgentComplementary::where('_id',$id)->unset('bucket_size');
        
        return response()->json(['status' => true, 'message'=>'Bucket-sizes Agent levels has been deleted successfully.']);
        
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $seasons = AgentComplementary::when($search != '', function($query) use ($search) {
                        $active = ($search == 'true' || $search == 'Active')? true: false;
                        $query->where('agent_level','like','%'.$search.'%')
                        ->orWhere('xps','like','%'.$search.'%')
                        ->orWhere('bucket_size','like','%'.$search.'%');
                    })
                    ->whereNotNull('bucket_size')
                    ->orderBy('agent_level','ASc')
                    ->skip($skip)
                    ->take($take)
                    ->get();

        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::when($search != '', function($query) use ($search) {
                        $query->where('complexity','like','%'.$search.'%')
                        ->orWhere('xps','like','%'.$search.'%')
                        ->orWhere('bucket_size','like','%'.$search.'%');
                    })
                    ->whereNotNull('bucket_size')
                    ->count();

        $admin = auth()->User();

        return DataTables::of($seasons)
        ->addIndexColumn()
        // ->editColumn('created_at', function($relic){
        //     return $relic->created_at->format('d-M-Y @ h:i A');
        // })
        ->addColumn('action', function($agentComplementary) use ($admin){
                $html = '';
                if($admin->hasPermissionTo('Edit Agent Levels')){
                    $html .= '<a href="javascript:void(0);" class="editAgentLevel" data-edit-path="'.route('admin.bucket-sizes.edit',$agentComplementary->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
                }

                if($admin->hasPermissionTo('Delete Agent Levels')){
                    $html .= ' <a href="'.route('admin.bucket-sizes.destroy',$agentComplementary->id).'" data-action="delete" data-toggle="modal" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
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
