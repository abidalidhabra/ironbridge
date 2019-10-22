<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Season;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SponserHuntController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.sponser-hunts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.sponser-hunts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $season = Season::create([ 
            'name'=> $request->season_name,
            'complexity'=> (int)$request->complexity,
            'active'=> $request->has('active')? true: false
        ]);
        $season->hunts()->createMany($request->hunts);
        return redirect()->route('admin.sponser-hunts.index');
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
        return view('admin.sponser-hunts.edit', [
            'season'=> Season::find($id)
        ]);
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

        $season = Season::find($id);
        $season->name = $request->season_name;
        $season->complexity = (int)$request->complexity;
        $season->active = $request->has('active')? true: false;
        $season->save();
        $season->hunts()->delete();
        $season->hunts()->createMany($request->hunts);
        return redirect()->back();
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

    public function huntHTML(Request $request)
    {
        if($request->ajax()) {
            return view('admin.sponser-hunts.partials.hunt-creation')->with('index', $request->index);
        }
        throw new Exception("You are not allow make this request");
    }

    public function clueHTML(Request $request)
    {
        if($request->ajax()) {
            return view('admin.sponser-hunts.partials.clue-creation')->with(['index'=> $request->index, 'huntIndex'=> $request->huntIndex]);
        }
        throw new Exception("You are not allow make this request");
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $seasons = Season::when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('name','like','%'.$search.'%')
            ->orWhere('active', $active)
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->orderBy('created_at','DESC')
        ->skip($skip)
        ->take($take)
        ->get();

        /** Filter Result Total Count  **/
        $filterCount = Season::when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('name','like','%'.$search.'%')
            ->orWhere('active', $active)
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->count();

        return DataTables::of($seasons)
        ->addIndexColumn()
        ->editColumn('created_at', function($event){
            return $event->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('active', function($event){
            return ($event->active)? "Active": "Inactive";
        })
        ->addColumn('action', function($event){
            return '<a href="'.route('admin.sponser-hunts.edit',$event->id).'" data-toggle="tooltip" title="Edit" >Edit</a>';
        })
        ->rawColumns(['action'])
        ->setTotalRecords(Season::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}