<?php

namespace App\Http\Controllers\Admin\AgentLevel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\v2\AgentComplementary;
use App\Models\v1\WidgetItem;
use App\Models\v1\Game;
use Yajra\DataTables\DataTables;
use Auth;
use Str;


class AvatarAgentLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.agent-levels.avatar.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $widgetItem = WidgetItem::orderBy('widget_name,avatar_id','desc')
                                ->with('avatar')
                                ->get()
                                ->groupBy('widget_name');

        $widgetItem->transform(function($value,$key){
            $value = $value->mapToGroups(function ($avatar, $key) {
                return [$avatar['avatar']['gender'] => $avatar];
            });
            return $value;
        });
        $agent_complementary = AgentComplementary::get();

        return view('admin.agent-levels.avatar.create',compact('widgetItem','agent_complementary'));
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
            'widgets.*'   => 'required',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $widgets = [];
        $outfits = [];
        foreach ($request->widgets as $key => $value) {
            $widgetsItem = Str::plural(strtolower($key)); 
            $widgets[$widgetsItem] = null;
            unset($value[0]);
            if (count(array_filter($value)) > 0) {
                $widgets[$widgetsItem] = array_values(array_filter($value));
                if ($key == 'Outfits') {
                    $outfits = WidgetItem::whereIn('_id',$value)->get()->pluck('items')->toArray();
                }
                if (isset($value[0]) && $value[0]=="all") {
                    $widgets[$widgetsItem] = WidgetItem::where('widget_name',$key)->get()->pluck('id')->toArray();
                }
            }
        }
        /* outfits */ 
        if (count($outfits) > 0) {
            $outfitsKey = [];
            foreach ($outfits as $items) {
                foreach ($items as $value) {
                    $outfitsKey[] = $value;
                }
            }
            $widgetsItemMulti = WidgetItem::whereIn('_id',$outfitsKey)->get()->groupBy('widget_name');
            foreach ($widgetsItemMulti as $key => $value) {
                if ( $widgets[Str::plural(strtolower($key))]!=null && count($widgets[Str::plural(strtolower($key))])>0) {
                    $widgets[Str::plural(strtolower($key))] = array_unique(array_merge($widgets[Str::plural(strtolower($key))],$value->pluck('id')->toArray()));
                } else {
                    $widgets[Str::plural(strtolower($key))] = $value->pluck('id')->toArray();
                }

            }
        }
        /* end outfits */

        $data = $request->all();

        $agent_complementary = AgentComplementary::where('agent_level',(int)$request['agent_level'])->first();
        $agent_complementary->widgets = $widgets;
        $agent_complementary->save();

        return response()->json(['status' => true,'message' => 'Avatar agent levels added! Please wait we are redirecting you.']);
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
        $widgetItem = WidgetItem::orderBy('widget_name,avatar_id','desc')
                                ->with('avatar')
                                ->get()
                                ->groupBy('widget_name');

        $widgetItem->transform(function($value,$key){
            $value = $value->mapToGroups(function ($avatar, $key) {
                return [$avatar['avatar']['gender'] => $avatar];
            });
            return $value;
        });
        $relicReward = AgentComplementary::find($id);
        $agent_complementary = AgentComplementary::get();
        return view('admin.agent-levels.avatar.edit',compact('widgetItem','relicReward','agent_complementary'));
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
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $widgets = [];
        $outfits = [];
        foreach ($request->widgets as $key => $value) {
            $widgetsItem = Str::plural(strtolower($key)); 
            $widgets[$widgetsItem] = null;
            unset($value[0]);
            if (count(array_filter($value)) > 0) {
                $widgets[$widgetsItem] = array_values(array_filter($value));
                if ($key == 'Outfits') {
                    $outfits = WidgetItem::whereIn('_id',$value)->get()->pluck('items')->toArray();
                }
                if (isset($value[0]) && $value[0]=="all") {
                    $widgets[$widgetsItem] = WidgetItem::where('widget_name',$key)->get()->pluck('id')->toArray();
                }
            }
        }
        
        /* outfits */ 
        if (count($outfits) > 0) {
            $outfitsKey = [];
            foreach ($outfits as $items) {
                foreach ($items as $value) {
                    $outfitsKey[] = $value;
                }
            }
            $widgetsItemMulti = WidgetItem::whereIn('_id',$outfitsKey)->get()->groupBy('widget_name');
            foreach ($widgetsItemMulti as $key => $value) {
                if ($widgets[Str::plural(strtolower($key))]!=null && count($widgets[Str::plural(strtolower($key))])>0) {
                    $array_merge = array_unique(array_merge($widgets[Str::plural(strtolower($key))],$value->pluck('id')->toArray()));
                    $widgets[Str::plural(strtolower($key))][] = (array)$array_merge;
                } else {
                    $widgets[Str::plural(strtolower($key))] = $value->pluck('id')->toArray();
                }

            }
        }
        /* end outfits */
             

        $data = $request->all();
        

        

        $agentComplementary = AgentComplementary::where('_id',$id)->first();
        $agentComplementary->widgets = $widgets;
        $agentComplementary->save();

        return response()->json(['status' => true,'message' => 'Avatar Agent levels updated! Please wait we are redirecting you.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AgentComplementary::where('_id',$id)->unset('widgets');
        return response()->json(['status' => true, 'message'=>'Avatar levels has been deleted successfully.']);
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $agent_complementary = AgentComplementary::whereNotNull('widgets')
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
        $filterCount = AgentComplementary::whereNotNull('widgets')
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
                if($admin->hasPermissionTo('Edit Agent Levels')){
                    $html .= '<a href="'.route('admin.avatar-agent-levels.edit',$relic->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox edit_agent" data-id="'.$relic->id.'"></i></a>';
                }

                if($admin->hasPermissionTo('Delete Agent Levels')){
                    $html .= ' <a href="'.route('admin.avatar-agent-levels.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                }
                
                //$html .= ' <a href="'.route('admin.hunts-agent-levels.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(AgentComplementary::whereNotNull('widgets')->count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
