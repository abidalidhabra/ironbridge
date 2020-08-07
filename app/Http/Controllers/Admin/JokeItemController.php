<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v3\JokeItem;
use App\Repositories\HuntStatisticRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JokeItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.joke_items_loots.index', [
            'huntStatistic'=> (new HuntStatisticRepository)->first(['_id', 'joke_item']),
            'items'=> JokeItem::all(),
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
        return response()->json(['message'=> 'Item has been retrieved successfully.', 'item'=> JokeItem::find($id)]);
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
            'id' => 'required|exists:joke_items,_id',
            'name' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png',
        ]);
        
        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $dataToBeUpdate['name'] = $request->name;
        
        if ($request->hasFile('image')) {
            $dataToBeUpdate['image'] = $request->image->hashName();
        }
        $status = JokeItem::where('_id', $request->id)->update($dataToBeUpdate);

        if ($status && $request->hasFile('image')) {
            $path = $request->file('image')->store('joke_items', 'public');
        }
        return response()->json(['message'=> 'Item has been updated successfully.', 'item'=> $request->only('name')]);
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

    public function updateProbability(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'probability' => 'required|numeric|integer|min:0|max:100',
        ]);
        
        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $data = (new HuntStatisticRepository)->first(['_id', 'joke_item']);
        $data->joke_item = ['min'=> 1, 'max'=> (int)$request->probability, 'out_of'=> 100];
        $data->save();
        return response()->json(['message' => 'Probability has been updated successfully.']);
    }
}
