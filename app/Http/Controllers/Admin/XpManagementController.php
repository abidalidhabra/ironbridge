<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v2\XpManagement;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Auth;


class XpManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.xpManagement.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.xpManagement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event'=> 'required',
            'xp'=> 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(), 'status' => false]);
        }

        $data = $request->all();
        $data['xp'] = (int)$data['xp'];
        XpManagement::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'XP Management has been added successfully.',
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
        $xpManagement = XpManagement::find($id);
        return view('admin.xpManagement.edit',compact('xpManagement'));
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
        $validator = Validator::make($request->all(), [
            'event'=> 'required',
            'xp'=> 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(), 'status' => false]);
        }

        $xpManagement = XpManagement::where('_id',$id)->first();
        $xpManagement->xp = (int)$request->xp;
        // $xpManagement->event = $request->event;
        $xpManagement->save();
        
        return response()->json([
            'status' => true,
            'message'=>'XP Management has been update successfully.',
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

    public function getXpManagementList(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $xpManagements = XpManagement::when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('event','like','%'.$search.'%')
            ->orWhere('xp','like','%'.$search.'%');
        })
        ->orderBy('created_at','DESC')
        ->skip($skip)
        ->take($take)
        ->get();

        /** Filter Result Total Count  **/
        $filterCount = XpManagement::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('event','like','%'.$search.'%')
            ->orWhere('xp','like','%'.$search.'%');
        })
        ->count();
                
        $admin = Auth::User();
        return DataTables::of($xpManagements)
        ->addIndexColumn()
        ->editColumn('created_at', function($xpManagement){
            return $xpManagement->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('event', function($xpManagement){
            return ucfirst(str_replace('_', ' ', $xpManagement->event));
        })
        ->addColumn('action', function($xpManagement) use($admin){
                $html = '';
                //if($admin->hasPermissionTo('Edit Main Relics')){
                    $html .= '<a href="'.route('admin.xpManagement.edit',$xpManagement->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
                //}

                    //$html .= ' <a href="'.route('admin.relics.destroy',$xpManagement->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';

            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(XpManagement::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
