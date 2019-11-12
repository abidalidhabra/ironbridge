<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use App\Models\v1\WidgetItem;
use App\Models\v2\AgentComplementary;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


class RelicRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.relics.rewards.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $games = Game::where('status',true)->get();
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

        return view('admin.relics.rewards.create',compact('games','widgetItem'));
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
            'xps'         => 'required|numeric',
            // 'minigames' => 'required|array',
            // 'complexity'  => 'required|numeric',
        ],[
            'xps.required'  => 'The XP points field is required.',
            // 'complexity.required'  => 'The difficulty field is required.'
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $widgets = [];
        foreach ($request->widgets as $key => $value) {
            $widgetsItem = Str::plural(strtolower($key)); 
            $widgets[$widgetsItem] = null;
            unset($value[0]);
            if (count(array_filter($value)) > 0) {
                $widgets[$widgetsItem] = array_values(array_filter($value));
                if (isset($value[0]) && $value[0]=="all") {
                    $widgets[$widgetsItem] = WidgetItem::where('widget_name',$key)->get()->pluck('id')->toArray();
                }
            }
        }
                
        $data = $request->all();
        if (isset($data['bucket_size']) && $data['bucket_size']!="") {
            $data['bucket_size'] =  (int)$data['bucket_size'];
        } else {
            unset($data['bucket_size']);
        }

        if (isset($data['complexity']) && $data['complexity']!="") {
            $data['complexity'] =  (int)$data['complexity'];
        } else {
            unset($data['complexity']);
        }

        if (isset($data['minigames']) && count($data['minigames']) > 0) {
            $data['minigames'] = $data['minigames'];
        } else {
            unset($data['minigames']);
        }

        $data['agent_level'] = (int)$request['agent_level'];
        $data['xps'] = (int)$request['xps'];
        $data['widgets'] = $widgets;
        
        AgentComplementary::create($data);
        
        return response()->json(['status' => true,'message' => 'Agent complementary added! Please wait we are redirecting you.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $relicReward = AgentComplementary::find($id);
        $games = [];
        if (isset($relicReward->minigames)) {        
            $games = Game::whereIn('_id',$relicReward->minigames)
                        ->get()
                        ->pluck('name');
        }

        return view('admin.relics.rewards.show', compact(['relicReward','games']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $games = Game::where('status',true)->get();
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
        return view('admin.relics.rewards.edit',compact('games','widgetItem','relicReward'));
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
            'xps'         => 'required|numeric',
            // 'minigames' => 'required|array',
            // 'complexity'  => 'required|numeric',
        ],[
            'xps.required'  => 'The XP points field is required.',
            // 'complexity.required'  => 'The difficulty field is required.'
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['status' => false,'message' => $validator->messages()->first()]);
        }

        $widgets = [];
        foreach ($request->widgets as $key => $value) {
            $widgetsItem = Str::plural(strtolower($key)); 
            $widgets[$widgetsItem] = null;
            unset($value[0]);
            if (count(array_filter($value)) > 0) {
                $widgets[$widgetsItem] = array_values(array_filter($value));
                if (isset($value[0]) && $value[0]=="all") {
                    $widgets[$widgetsItem] = WidgetItem::where('widget_name',$key)->get()->pluck('id')->toArray();
                }
            }
        }
        
        $data = $request->all();
        if ($data['bucket_size']!="") {
            $data['bucket_size'] =  (int)$data['bucket_size'];
        }
        if ($data['complexity']!="") {
            $data['complexity'] =  (int)$data['complexity'];
        }

        if (isset($data['minigames']) && count($data['minigames']) > 0) {
            $data['minigames'] =  $data['minigames'];
        }

        $data['agent_level'] = (int)$request['agent_level'];
        $data['xps'] = (int)$request['xps'];
        // $data['complexity'] = (int)$request['complexity'];
        $data['widgets'] = $widgets;

        $agentComplementary = AgentComplementary::find($id);
        $agentComplementary->update($data);
        if ($data['bucket_size']=="") {
            $agentComplementary->unset('bucket_size');
        }
        if ($data['complexity']=="") {
            $agentComplementary->unset('complexity');
        }
        if (!isset($data['minigames'])) {
            $agentComplementary->unset('minigames');
        }
        return response()->json(['status' => true,'message' => 'Agent complementary updated! Please wait we are redirecting you.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AgentComplementary::find($id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'Agent complementary has been deleted successfully.',
        ]);
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
            ->orWhere('complexity','like','%'.$search.'%')
            ->orWhere('widgets','like','%'.$search.'%')
            ->orWhere('xps','like','%'.$search.'%');
        })
        ->orderBy('created_at','DESC')
        ->skip($skip)
        ->take($take)
        ->get();

        /** Filter Result Total Count  **/
        $filterCount = AgentComplementary::when($search != '', function($query) use ($search) {
            $query->where('complexity','like','%'.$search.'%')
            ->orWhere('xps','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%')
            ->orWhere('widgets','like','%'.$search.'%')
            ->orWhere('xps','like','%'.$search.'%');
        })
        ->count();

        return DataTables::of($seasons)
        ->addIndexColumn()
        ->editColumn('complexity', function($relic){
            return ($relic->complexity)?$relic->complexity:'-';
        })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('action', function($relic){
                $html = '<a href="'.route('admin.relicReward.edit',$relic->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';

                $html .= ' <a href="'.route('admin.relicReward.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';

                $html .= ' <a href="'.route('admin.relicReward.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(AgentComplementary::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
