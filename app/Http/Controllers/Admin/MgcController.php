<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v2\MGCLoot;
use App\Models\v1\WidgetItem;
use App\Models\v2\Relic;
use Validator;
use Auth;
use Session;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;


class MgcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loot = MGCLoot::with('relics_info:_id,name,number,mgc_loot_tables')->get();
        $loots = $loot->groupBy('reward_type');
        return view('admin.mgc_loots.index',compact('loots','loot'));
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
        $loot = MGCLoot::with('relics_info:_id,name,number,mgc_loot_tables')->get();
        $relics = Relic::where(['active'=>true])->get();
        $lootReward = $loot->groupBy('reward_type');
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();
        
        return view('admin.mgc_loots.edit_rewards',compact('lootReward','widgetItem','loot','relics'));
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
        if (!isset($request['relics'])) {
            $request['relics'] = '';
        }
        $validator = Validator::make($request->all(),[
            'status'                  => 'required',
            'relics'                  => 'required',
            'gold_value.*'            => 'required_if:reward_type,avatar_item_and_gold,gold,skeleton_key_and_gold|numeric',
            //'min_range' => 'required|integer',
            //'max_range' => 'required|integer',
            // 'type.*'       => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'widget_name.*'           => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'skeletons.*'             => 'required_if:reward_type,skeleton_key,skeleton_key_and_gold|numeric',
            'possibility.*'           => 'required|numeric',
            'widgets_possibility.*.*' => 'required|numeric',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $relics = Relic::whereIn('_id',$request->relics)
                            ->get();

        /*foreach ($relics as $key => $relic) {
            if($relic->loot_info->count() > 0){
                if ($relic->mgc_loot_info->first()) {
                    return response()->json(['message' => 'Relic '.$relic->name.' already associated with other loot table.'],422);
                }
            }
        }*/
        
        $possibility = $request->get('possibility');
        if (array_sum($possibility) != 100) {
            return response()->json(['status' => false,'message' => 'A total sum of possibilities needs equal to 100']);
        }
        if (isset($request->widgets_possibility) && $request->widgets_possibility != "") {
            $widgetsPossibility = array_values($request->get('widgets_possibility'));
            foreach ($request->widgets_possibility as $key => $value) {
                if (array_sum($value) != 100) {
                    return response()->json(['status' => false,'message' => 'A total sum of possibilities needs equal to 100']);
                }
            }
        }
        $allData = $request->all();
        $i = 0;
        
        $loots = MGCLoot::get();
        
        foreach ($loots as $key => $value) {
            $data = [];
            $huntRewardId = $value->id;
            $value->possibility = (float)$possibility[$huntRewardId];
            $value->min_range = (int)1+$i;
            $i += $possibility[$huntRewardId]*10;
            $value->max_range = (int)$i;

            if ($value->reward_type == 'gold') {
                $value->gold_value = (int)$allData['gold_value'][$huntRewardId];
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
                $value->gold_value = (int)$allData['gold_value'][$huntRewardId];

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
                $value->gold_value = (int)$allData['gold_value'][$huntRewardId];
                $value->skeletons = (int)$allData['skeletons'][$huntRewardId];
            }

            $value->status = ($request->status=="active")?true:false;
            if ($value->relics) {
                Relic::whereIn('_id',$value->relics)->pull('mgc_loot_tables', $value->id);
            }
            $value->relics = $request->relics;
            $value->save();
            Relic::whereIn('_id',$request->relics)->push('mgc_loot_tables', $value->id);
        }
        return response()->json([
            'status' => true,
            'message'=>'MGC Loots has been updated successfully.',
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
}
