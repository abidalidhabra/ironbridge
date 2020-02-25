<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\v2\Plan;
use App\Models\v1\Country;
use Validator;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::get();

        return view('admin.plans.index',compact('plans'));
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

        $plan = Plan::where('_id',$id)->first();
        $countries = Country::get();
        $html = view('admin.plans.edit',compact('plan','countries'))->render();

        return [
                    'status' => true,
                    'html'=>$html,
                ];


        return view('admin.plans.edit',compact('plan','countries'));
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
        $plan = Plan::where('_id',$id)->first();

        $input = $request->all();
        $input['type'] = $plan->type;

        $validator = Validator::make($input,[
            'name'              => 'required',
            'price'             => 'required_if:type,gold',
            'gold_value'        => 'numeric|integer|required_if:type,gold',
            'skeletons_bucket'  => 'numeric|integer|required_if:type,skeleton_bucket',
            'skeleton_keys'     => 'numeric|integer|required_if:type,skeleton',
            'bucket'            => 'numeric|integer|required_if:type,chest_bucket',
            'compasses'         => 'numeric|integer|required_if:type,compass',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $plan->name = $input['name'];
        $plan->country_id = $plan->country_id;
        if ($input['type'] == 'gold') {
            $plan->price = (float)$input['price'];
            $plan->gold_value = (int)$input['gold_value'];
        } else if ($input['type'] == 'skeleton_bucket'){
            $plan->skeletons_bucket = (int)$input['skeletons_bucket'];
            $plan->gold_price = (int)$input['gold_price'];
        } else if ($input['type'] == 'skeleton'){
            $plan->skeleton_keys = (int)$input['skeleton_keys'];
            $plan->gold_price = (int)$input['gold_price'];
        } else if ($input['type'] == 'chest_bucket'){
            $plan->bucket = (int)$input['bucket'];
            $plan->gold_price = (int)$input['gold_price'];
        } else if ($input['type'] == 'compass'){
            $plan->compasses = (int)$input['compasses'];
            $plan->gold_price = (int)$input['gold_price'];
        }

        $plan->save();
        return response()->json([
            'status' => true,
            'message'=>'Plan has been updated successfully.',
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

    public function list(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $plans = Plan::when($search != '', function($query) use ($search) {
                        $active = ($search == 'true' || $search == 'Active')? true: false;
                        $query->where('agent_level','like','%'.$search.'%')
                        ->orWhere('xps','like','%'.$search.'%')
                        ->orWhere('bucket_size','like','%'.$search.'%');
                    })
                    ->skip($skip)
                    ->take($take)
                    ->get();


        /** Filter Result Total Count  **/
        $filterCount = Plan::when($search != '', function($query) use ($search) {
                        $query->where('complexity','like','%'.$search.'%')
                        ->orWhere('xps','like','%'.$search.'%')
                        ->orWhere('bucket_size','like','%'.$search.'%');
                    })
                    ->count();
        

        $admin = auth()->User();

        return DataTables::of($plans)
        ->addIndexColumn()
        ->addColumn('country', function($plan){
            return $plan->country->name;
        })
        ->addColumn('action', function($plan) use ($admin){
                $html = '';
                    $html .= '<a href="javascript:void(0);" class="editAgentLevel" data-edit-path="'.route('admin.bucket-sizes.edit',$plan->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';

                    $html .= ' <a href="'.route('admin.bucket-sizes.destroy',$plan->id).'" data-action="delete" data-toggle="modal" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'country'])
        // ->setTotalRecords(Plan::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
