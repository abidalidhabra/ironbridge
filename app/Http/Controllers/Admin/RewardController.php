<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use App\Models\v2\HuntReward;
use App\Models\v1\WidgetItem;
use Validator;
use Auth;

class RewardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $huntReward = HuntReward::get();

        $complexity1 = $huntReward->where('complexity',1)->groupBy('reward_type');
        $complexity2 = $huntReward->where('complexity',2)->groupBy('reward_type');
        $complexity3 = $huntReward->where('complexity',3)->groupBy('reward_type');
        $complexity4 = $huntReward->where('complexity',4)->groupBy('reward_type');
        $complexity5 = $huntReward->where('complexity',5)->groupBy('reward_type');
        
        return view('admin.rewards.index',compact('huntReward','complexity1','complexity2','complexity3','complexity4','complexity5'));
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
        $huntReward = HuntReward::where('_id',$id)->first();
        $widgetItem = WidgetItem::groupBy('widget_name')->groupBy('widget_category')->get();
        
        
        return view('admin.rewards.edit_rewards',compact('huntReward','widgetItem'));
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
            'gold_value' => 'required_if:reward_type,avatar_item_and_gold,gold,skeleton_key_and_gold',
            //'min_range'  => 'required|integer',
            //'max_range'  => 'required|integer',
            // 'type.*'        => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'widget_name.*'  => 'required_if:reward_type,avatar_item,avatar_item_and_gold',
            'skeletons'  => 'required_if:reward_type,skeleton_key,skeleton_key_and_gold',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $rewardtype = $request->get('reward_type');
        $allData = $request->all();
        
        //$data['min_range'] = (int)$allData['min_range'];
        //$data['max_range'] = (int)$allData['max_range'];
        if ($rewardtype == 'avatar_item') {
            $data['widgets_order'] = [];
            for ($i=0; $i < count($allData['widget_name']) ; $i++) {
                $widgetsOrder = explode(',',str_replace('__', ',', $allData['widget_name'][$i]));
                $data['widgets_order'][] = [
                                            'type'        => $widgetsOrder[1],
                                            'widget_name' => $widgetsOrder[0],
                                            'min'   => (int)$allData['min'][$i],
                                            'max'   => (int)$allData['max'][$i]
                                            ];
            } 
        }

        if ($rewardtype == 'avatar_item_and_gold') {
            $data['widgets_order'] = [];
            for ($i=0; $i < count($allData['widget_name']) ; $i++) {
                $widgetsOrder = explode(',',str_replace('__', ',', $allData['widget_name'][$i]));
                $data['widgets_order'][] = [
                                            'type'        => $widgetsOrder[1],
                                            'widget_name' => $widgetsOrder[0]
                                            ];
            } 
            $data['gold_value'] = (float)$allData['gold_value'];
        }

        if ($rewardtype == 'gold') {
            $data['gold_value'] = (float)$allData['gold_value'];
        }

        if ($rewardtype == 'skeleton_key') {
            $data['skeletons'] = (int)$allData['skeletons'];
        }

        if ($rewardtype == 'skeleton_key_and_gold') {
            $data['skeletons'] = (int)$allData['skeletons'];
            $data['gold_value'] = (float)$allData['gold_value'];
        }


        $huntReward = HuntReward::where('_id',$id)
                                    ->update($data);

        if ($huntReward) {
            return response()->json([
                'status' => true,
                'message'=>'Hunt reward has been updated successfully.',
            ]);
        }
        return response()->json([
                'status' => false,
                'message'=>'Please try again.',
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
