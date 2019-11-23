<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v2\Loot;
use App\Models\v1\WidgetItem;
use Validator;
use Auth;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;


class LootController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loots = Loot::get()->groupBy('number')->sortKeysDesc();
        return view('admin.loots.index',compact('loots'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();
        return view('admin.loots.create',compact('widgetItem'));   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!isset($request['gold_check'])) {
            $request['gold_check'] = 'false';
        }
        if (!isset($request['skeleton_key_check'])) {
            $request['skeleton_key_check'] = 'false';
        }
        if (!isset($request['skeleton_gold_check'])) {
            $request['skeleton_gold_check'] = 'false';
        }
        if (!isset($request['avatar_check'])) {
            $request['avatar_check'] = 'false';
        }

        $validator = Validator::make($request->all(),[
            'status'                    => 'required',
            'gold.*.*'                  => 'required_if:gold_check,==,true',
            'skeleton_key.*.*'          => 'required_if:skeleton_key_check,==,true',
            'skeleton_key_and_gold.*.*' => 'required_if:skeleton_gold_check,==,true',
            'avatar_item.*.*'           => 'required_if:avatar_check,==,true',
            // 'avatar_item.*.*.*'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();
        $possibility = [];
        foreach ($data as $key => $value) {
            if ($key=="gold" && isset($value['possibility']) && $data['gold_check'] == 'true') {
                $possibility[] = array_sum($value['possibility']);
            }
            if ($key=="skeleton_key" && isset($value['possibility']) && $data['skeleton_key_check'] == 'true') {
                $possibility[] = array_sum($value['possibility']);
            }
            if ($key=="skeleton_key_and_gold" && isset($value['possibility']) && $data['skeleton_gold_check'] == 'true') {
                $possibility[] = array_sum($value['possibility']);
            }
            if ($key=="avatar_item" && isset($value['possibility']) && $data['avatar_check'] == 'true') {
                $possibility[] = array_sum($value['possibility']);
                
                if (isset($value['widgets_order'])) {
                    foreach ($value['widgets_order']['possibility'] as $index => $widget) {
                        if (array_sum($widget) != 100) {
                            return response()->json(['status' => false,'message' => 'A total sum of widgets possibilities needs equal to 100']);
                        }
                    }
                }
            }

        }

        if (array_sum($possibility) != 100) {
            return response()->json(['status' => false,'message' => 'A total sum of possibilities needs equal to 100']);
        }

        $a = 0;
        $number = Loot::orderBy('number','desc')->first()->number+1;
        
        foreach ($data as $key => $value) {
            $loot = ($data['status']=="active")?true:false;
            if ($key == 'gold' && $data['gold_check'] == 'true') {
                for ($i=0; $i < count($value['possibility']) ; $i++) {
                    $maxRange =  (int)array_values($value['possibility'])[$i]*10;

                    $loot = [
                                    'possibility' => (int)array_values($value['possibility'])[$i],
                                    'gold_value'  => (int)array_values($value['gold'])[$i],
                                    'min_range'   => (int)$a+1,
                                    'max_range'   => (int)$maxRange+$a,
                                    'number'      => (int)$number,
                                    'reward_type' => $key,
                                    'status'      => ($data['status']=="active")?true:false,
                                ];
                    $a += $maxRange;
                    Loot::create($loot);
                }
            }

            if ($key == 'skeleton_key' && $data['skeleton_key_check'] == 'true') {
                for ($i=0; $i < count($value['possibility']) ; $i++) {
                    $maxRange =  (int)$value['possibility'][$i]*10;
                    $loot = [
                                    'possibility' => (int)array_values($value['possibility'])[$i],
                                    'skeletons'    => (int)$value['skeleton'][$i],
                                    'min_range'   => (int)$a+1,
                                    'max_range'   => (int)$maxRange+$a,
                                    'number'   => (int)$number,
                                    'reward_type' => $key,
                                    'status'      => ($data['status']=="active")?true:false,
                                ];
                     $a += $maxRange;
                     Loot::create($loot); 
                }
            }
            if ($key == 'skeleton_key_and_gold' && $data['skeleton_gold_check'] == 'true') {
                for ($i=0; $i < count(array_values($value['possibility'])) ; $i++) {
                    $maxRange =  (int)array_values($value['possibility'])[$i]*10;
                    $loot = [
                                    'possibility' => (int)array_values($value['possibility'])[$i],
                                    'skeletons'   => (int)$value['skeleton'][$i],
                                    'gold_value'  => (int)$value['gold'][$i],
                                    'min_range'   => (int)$a+1,
                                    'max_range'   => (int)$maxRange+$a,
                                    'number'   => (int)$number,
                                    'reward_type' => $key,
                                    'status'      => ($data['status']=="active")?true:false,
                                ];
                     $a += $maxRange;
                    Loot::create($loot);
                }   
            }
            if ($key == 'avatar_item' && $data['avatar_check'] == 'true') {
                for ($i=0; $i < count(array_values($value['possibility'])) ; $i++) {
                    $maxRange =  (int)array_values($value['possibility'])[$i]*10;
                    $widgetPossibility = [];
                    $m = 0;
                    for ($w=0; $w < count($value['widgets_order']['possibility'][$i]) ; $w++) { 
                        $widgetsOrder = explode(',',str_replace('__', ',', $value['widgets_order']['widget_name'][$i][$w]));
                        $max = $value['widgets_order']['possibility'][$i][$w]*10; 
                        $widgetPossibility[] = [
                                                'type' =>    $widgetsOrder[1],      
                                                'widget_name' =>    $widgetsOrder[0],      
                                                'possibility' =>(int)$value['widgets_order']['possibility'][$i][$w],      
                                                'min' => (int)1+$m,      
                                                'max' => (int)$max+$m,
                                                
                                            ];    
                        $m += $max; 
                    }
                    $loot = [
                                    'possibility'   => (int)array_values($value['possibility'])[$i],
                                    'widgets_order' => $widgetPossibility,
                                    'min_range'   => (int)1+$a,
                                    'max_range'   => (int)$maxRange+$a,
                                    'number'   => (int)$number,
                                    'reward_type' => $key,
                                    'status'      => ($data['status']=="active")?true:false,
                                ];
                     $a += $maxRange; 
                    Loot::create($loot);
                }
            }


            
        }       
            return response()->json([
                'status' => true,
                'message'=>'Loots has been created successfully.',
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loots = Loot::where('number',(int)$id)->get()->groupBy('reward_type');
        
        return view('admin.loots.show',compact('loots','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $huntReward = Loot::where('number',(int)$id)->get()->groupBy('reward_type');
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();
        
        return view('admin.loots.edit_rewards',compact('huntReward','widgetItem','id'));
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
            'gold_value.*' => 'required_if:reward_type,avatar_item_and_gold,gold,skeleton_key_and_gold|numeric',
            //'min_range'  => 'required|integer',
            //'max_range'  => 'required|integer',
            // 'type.*'        => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'widget_name.*'  => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'skeletons.*'  => 'required_if:reward_type,skeleton_key,skeleton_key_and_gold|numeric',
            'possibility.*'  => 'required|numeric',
            'widgets_possibility.*.*'  => 'required|numeric',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        
        $possibility = $request->get('possibility');
        if (array_sum($possibility) != 100) {
            return response()->json(['status' => false,'message' => 'A total sum of possibilities needs equal to 100']);
        }
        $widgetsPossibility = array_values($request->get('widgets_possibility')); 
        
        if (array_sum($widgetsPossibility[0]) != 100) {
            return response()->json(['status' => false,'message' => 'A total sum of possibilities needs equal to 100']);
        }

        $allData = $request->all();
        $i = 0;
        
        $huntReward = Loot::where('number',(int)$id)->get();
        
        foreach ($huntReward as $key => $value) {
            $data = [];
            $huntRewardId = $value->id;
            $value->possibility = (float)$possibility[$huntRewardId];
            $value->min_range = (int)1+$i;
            $i += $possibility[$huntRewardId]*10;
            $value->max_range = (int)$i;

            if ($value->reward_type == 'gold') {
                $value->gold_value = (float)$allData['gold_value'][$huntRewardId];
            }
            if ($value->reward_type == 'avatar_item') {
                $data['widgets_order'] = [];
                
                $m = 0;
                for ($k=0; $k < count($allData['widget_name'][$huntRewardId]) ; $k++) {
                    $widgetsOrder = explode(',',str_replace('__', ',', $allData['widget_name'][$huntRewardId][$k]));
                    $max = $allData['widgets_possibility'][$huntRewardId][$k]*10; 
                    $data['widgets_order'][] = [
                                                'type'        => $widgetsOrder[1],
                                                'widget_name' => $widgetsOrder[0],
                                                'min'         => (int)1+$m,
                                                'max'         => (int)$max+$m,
                                                'possibility' => (float)$allData['widgets_possibility'][$huntRewardId][$k],
                                                ];
                    $m += $max;

                }
                $value->widgets_order = $data['widgets_order'];
            }
            if ($value->reward_type == 'avatar_item_and_gold') {
                $value->gold_value = (float)$allData['gold_value'][$huntRewardId];

                $data['widgets_order'] = [];
                $m = 0;
                for ($k=0; $k < count($allData['widget_name'][$huntRewardId]) ; $k++) {
                    $widgetsOrder = explode(',',str_replace('__', ',', $allData['widget_name'][$huntRewardId][$k]));
                    $max = $allData['widgets_possibility'][$huntRewardId][$k]*10; 
                    $data['widgets_order'][] = [
                                                'type'        => $widgetsOrder[1],
                                                'widget_name' => $widgetsOrder[0],
                                                'min'         => (int)1+$m,
                                                'max'         => (int)$max+$m,
                                                'possibility' => (float)$allData['widgets_possibility'][$huntRewardId][$k],
                                                ];
                    $m += $max;

                }
                $value->widgets_order = $data['widgets_order'];
            }
            if ($value->reward_type == 'skeleton_key') {
                $value->skeletons = (int)$allData['skeletons'][$huntRewardId];
            }
            if ($value->reward_type == 'skeleton_key_and_gold') {
                $value->gold_value = (float)$allData['gold_value'][$huntRewardId];
                $value->skeletons = (int)$allData['skeletons'][$huntRewardId];
            }
            $value->save();
        }

        return response()->json([
            'status' => true,
            'message'=>'Hunt reward has been updated successfully.',
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
        Loot::where('number',(int)$id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'Delete successfully.',
        ]);
    }

    public function getLootsList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $loots = Loot::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        ->groupBy('number')
        ->skip($skip)
        ->take($take)
        ->get();
        /** Filter Result Total Count  **/
        $filterCount = Loot::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%');
        })
        ->groupBy('number')
        ->count();

        $admin = Auth::User();
        return DataTables::of($loots)
        ->addIndexColumn()
        ->addColumn('number', function($loot){
            return $loot->number;
        })
        ->addColumn('complexity', function($loot){
            return $loot->complexity;
        })
        ->addColumn('action', function($loot) use($admin){
            $html = '';
                //$html .= '<a href="'.route('admin.relics.edit',$loot->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';

            $html .= ' <a href="'.route('admin.relics.destroy',$loot->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';

            $html .= ' <a href="'.route('admin.relics.show',$loot->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(Loot::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }


    public function goldHTML(Request $request){
        if($request->ajax()) {
            return view('admin.loots.reward.gold')->with(['index'=> $request->index]);
        }
        throw new Exception("You are not allow make this request");
    }

    public function skeletonHTML(Request $request){
        if($request->ajax()) {
            return view('admin.loots.reward.skeleton')->with(['index'=> $request->index]);
        }
        throw new Exception("You are not allow make this request");   
    }

    public function skeletonGoldHTML(Request $request){
        if($request->ajax()) {
            return view('admin.loots.reward.skeleton_gold')->with(['index'=> $request->index]);
        }
        throw new Exception("You are not allow make this request");        
    }

    public function avatarHTML(Request $request){
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();
        
        if($request->ajax()) {
            return view('admin.loots.reward.avatar')->with(['index'=> $request->index,'widgetItem'=>$widgetItem]);
        }
        throw new Exception("You are not allow make this request");                
    }

    public function widgetHTML(Request $request){
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();

        if($request->ajax()) {
            return view('admin.loots.reward.widget')
                    ->with([
                        'parent_index'=> $request->parent_index,
                        'current_index'=> $request->current_index, 
                        'widgetItem'=>$widgetItem
                    ]);
        }
        throw new Exception("You are not allow make this request");                

    }

    public function changeStatus(Request $request){
        Loot::where('number',(int)$request->id)->update(['status'=>($request->status=='true')?true:false]);
        return response()->json([
            'status' => true,
            'message'=>'Status has been changed successfully.',
        ]);
    }
}
