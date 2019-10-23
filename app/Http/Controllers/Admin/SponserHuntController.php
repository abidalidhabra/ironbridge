<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Season;
use App\Services\Hunt\SponserHunt\AllotGameToClueService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SponserHuntController extends Controller
{
    private $allotGameToClueService;

    public function __construct() {
        $this->allotGameToClueService = new AllotGameToClueService;
    }

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
        $validatedData = $request->validate([
            'season_name'=> 'required|min:5',
            'hunts.*.name'=> 'required|string|min:5',
            'hunts.*.clues.*.complexity'=> 'required|numeric|integer|min:1',
            'hunts.*.clues.*.name'=> 'required|string|min:5',
            'hunts.*.clues.*.description'=> 'required|string|min:5'
        ]);

        $season = Season::create([ 
            'name'=> $request->season_name,
            'active'=> $request->has('active')? true: false
        ]);
        $season->hunts()->createMany($this->allotGameToClueService->allot($request));
        return response()->json(['status'=> true, 'message'=> 'Season added! Please wait we are redirecting you.']);
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

        $validatedData = $request->validate([
            'season_name'=> 'required|min:5',
            'hunts.*.name'=> 'required|string|min:5',
            'hunts.*.clues.*.complexity'=> 'required|numeric|integer|min:1',
            'hunts.*.clues.*.name'=> 'required|string|min:5',
            'hunts.*.clues.*.description'=> 'required|string|min:5'
        ]);

        $season = Season::find($id);
        $season->name = $request->season_name;
        $season->active = $request->has('active')? true: false;
        $season->save();
        $season->hunts()->delete();
        $season->hunts()->createMany($this->allotGameToClueService->allot($request));
        return response()->json(['status'=> true, 'message'=> 'Season updated! Please wait we are redirecting you.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $season = Season::find($id);
        if ($season) {
            $season->hunts()->delete();
            $season->delete();
            return response()->json(['status'=> true, 'message'=> 'Seasonal hunts deleted!']);
        }else {
            return response()->json(['status'=> false, 'message'=> 'Season hunts cannot delete']);
        }
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
            return view('admin.sponser-hunts.partials.clue-creation')->with(['index'=> $request->index, 'parentIndex'=> $request->parentIndex]);
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
        ->editColumn('created_at', function($season){
            return $season->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('active', function($season){
            return ($season->active)? "Active": "Inactive";
        })
        ->addColumn('action', function($season){
            if(auth()->user()->hasPermissionTo('Edit Seasonal Hunt')){
                $html = '<a href="'.route('admin.sponser-hunts.edit',$season->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }

            if(auth()->user()->hasPermissionTo('Delete Seasonal Hunt')){
                $html .= ' <a href="'.route('admin.sponser-hunts.destroy',$season->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
            }
            return $html;
        })
        ->rawColumns(['action'])
        ->setTotalRecords(Season::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
