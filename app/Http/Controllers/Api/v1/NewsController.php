<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\News;
use Illuminate\Http\Request;
use Validator;

class NewsController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* Validate the incoming request */
        $validator = Validator::make($request->all(),[
                        'page'       => "required|numeric|min:1",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

        /* Get the parameters */
        $page = ($request->get('page') - 1);
        $take = 10;
        $skip = $page * $take;

        $news = News::where('valid_till','>',now())->paginate();
        
        return response()->json(["message"=> "News has been retrieved successfully.", 'news' => $news->all(), 'last_page' => $news->lastPage()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /* Validate the incoming request */
        $validator = Validator::make($request->all(),[
                        'subject'       => "required|string",
                        'description'   => "required|string",
                        'valid_till'    => "required|string|date_format:Y-m-d H:i:s",
                    ]);
        
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()], 422);
        }

        return response()->json(News::create([
            'subject'     => $request->subject,
            'description' => $request->description,
            'valid_till'  => $request->valid_till,
        ]));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
