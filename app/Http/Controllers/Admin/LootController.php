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
        $loots = Loot::where('number',(int)$id)->get();
        $complexity1 = $loots->where('complexity',1)->groupBy('reward_type');
        $complexity2 = $loots->where('complexity',2)->groupBy('reward_type');
        $complexity3 = $loots->where('complexity',3)->groupBy('reward_type');
        $complexity4 = $loots->where('complexity',4)->groupBy('reward_type');
        $complexity5 = $loots->where('complexity',5)->groupBy('reward_type');
        
        return view('admin.loots.show',compact('loots','complexity1','complexity2','complexity3','complexity4','complexity5'));
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

                //$html .= ' <a href="'.route('admin.relics.destroy',$loot->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';

                $html .= ' <a href="'.route('admin.relics.show',$loot->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(Loot::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
