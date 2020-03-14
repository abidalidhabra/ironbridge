<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\HuntStatisticRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MapPiecesLootController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.map_pieces_loots.index', [
            'huntStatistic'=> (new HuntStatisticRepository)->first(['_id', 'map_pieces'])
        ]);
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'probability' => 'required|numeric|integer|min:1|max:100',
        ]);
        
        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $data = (new HuntStatisticRepository)->first(['_id', 'map_pieces']);
        $data->map_pieces = ['min'=> 1, 'max'=> (int)$request->probability, 'out_of'=> 100];
        $data->save();
        return response()->json(['message' => 'Probability has been updated successfully.']);
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
