<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v2\HuntStatistic;
use Validator;

class HuntStatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $huntStatistic =  HuntStatistic::first();
        return view('admin.hunt_statistics',compact('huntStatistic'));
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
        //
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
            'power_ratio'                   => 'required|numeric',
            'gold'                          => 'required|numeric',
            'skeleton_keys'                 => 'required|numeric',
            'boost_power_till'              => 'required|numeric',
            'refreshable_random_hunt'       => 'required|numeric',
            'nodes'                         => 'required|numeric',
            'distances_random_hunt'         => 'required|numeric',
            'relic'                         => 'required|numeric',
            'power'                         => 'required|numeric',
            'mgc'                           => 'required|numeric',
            'chest_xp'                      => 'required|numeric',
            'mg_change_charge'             => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $huntStatistic = HuntStatistic::where('_id',$id)->first();
        $huntStatistic->power_ratio =  (int)$request->power_ratio;
        $huntStatistic->gold =  (int)$request->gold;
        $huntStatistic->skeleton_keys =  (int)$request->skeleton_keys;
        $huntStatistic->boost_power_till =  (int)$request->boost_power_till;
        $huntStatistic->chest_xp =  (int)$request->chest_xp;
        $huntStatistic->mg_change_charge =  (int)$request->mg_change_charge;
        $huntStatistic->refreshable_distances =  (object)[
                                                    'random_hunt'=>(int)$request->refreshable_random_hunt,
                                                    'nodes'=>(int)$request->nodes,
                                                ];
        $huntStatistic->distances =  (object)[
                                        'random_hunt'=>(int)$request->distances_random_hunt,
                                        'relic'=>(int)$request->relic,
                                    ];
        $huntStatistic->freeze_till =  (object)[
                                        'power'=>(int)$request->power,
                                        'mgc'=>(int)$request->mgc,
                                    ];
        $huntStatistic->save();

        return response()->json([
                                'status' => true,
                                'message' => 'Hunt statistics has been updated successfully.'
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
