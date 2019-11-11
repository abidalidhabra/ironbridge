<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Relic;
use App\Models\v2\Season;
use App\Services\Hunt\SponserHunt\AllotGameToClueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class RelicController_old extends Controller
{

    private $disk = 'public';

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
        return view('admin.seasons.relics.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.seasons.relics.create', ['seasons'=> Season::active()->get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $season_slug)
    {

        $validator = Validator::make($request->all(), [
            'relic_name'=> 'required',
            'relic_desc'=> 'required|unique:seasons,slug',
            'active'=> 'nullable|in:true',
            'icon'=> 'required|image',
            'fees'=> 'required|numeric|integer',
            // 'active_icon'=> 'required|image',
            // 'inactive_icon'=> 'required|image',
            'complexity'=> 'required|numeric|integer:min:1',
            'clues.*.name'=> 'required|string',
            'clues.*.desc'=> 'required|string',
            'clues.*.radius'=> 'required|numeric|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $season = Season::where('slug', $season_slug)->select('_id', 'name')->first();
        
        $request->icon->store('seasons/'.$season->id, $this->disk);
        // $request->active_icon->store('seasons/'.$season->id, $this->disk);
        // $request->inactive_icon->store('seasons/'.$season->id, $this->disk);
        
        $season->relics()->create([
            'name'=> $request->relic_name,
            'desc'=> $request->relic_desc,
            'active'=> $request->has('active')? true: false,
            'icon'=> $request->icon->hashName(),
            'fees'=> (float)$request->fees,
            // 'active_icon'=> $request->active_icon->hashName(),
            // 'inactive_icon'=> $request->inactive_icon->hashName(),
            'complexity'=> (int)$request->complexity,
            'clues'=> $this->allotGameToClueService->allot($request),
        ]);
         
        return response()->json(['status'=> true, 'message'=> 'Relic added! Please wait we are redirecting you.']);
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
        $relic = Relic::find($id);
        $seasons = Season::active()->get(['id', 'name']);
        return view('admin.seasons.relics.edit', ['relic'=> $relic, 'seasons'=> $seasons]);
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
            'relic_name'=> 'required',
            'relic_desc'=> 'required|unique:seasons,slug',
            'active'=> 'nullable|in:true',
            'season_id'=> 'required|exists:seasons,_id',
            'fees'=> 'required|numeric|integer',
            // 'icon'=> 'nullable|image',
            // 'active_icon'=> 'nullable|image',
            // 'inactive_icon'=> 'nullable|image',
            'complexity'=> 'required|numeric|integer:min:1',
            'clues.*.name'=> 'required|string',
            'clues.*.desc'=> 'required|string',
            'clues.*.radius'=> 'required|numeric|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $relic = Relic::find($id);

        if ($request->hasFile('icon')) {
            Storage::disk($this->disk)->delete('seasons/'.$relic->season_id.'/'.$relic->icon);
            $request->icon->store('seasons/'.$relic->season_id, $this->disk);
            $relic->icon = $request->icon->hashName();
        }

        // if ($request->hasFile('active_icon')) {
        //     Storage::disk($this->disk)->delete('seasons/'.$relic->season_id.'/'.$relic->active_icon);
        //     $request->active_icon->store('seasons/'.$relic->season_id, $this->disk);
        //     $relic->active_icon = $request->active_icon->hashName();
        // }

        // if ($request->hasFile('inactive_icon')) {
        //     Storage::disk($this->disk)->delete('seasons/'.$relic->season_id.'/'.$relic->inactive_icon);
        //     $request->inactive_icon->store('seasons/'.$relic->season_id, $this->disk);
        //     $relic->inactive_icon = $request->inactive_icon->hashName();
        // }

        $relic->season_id = $request->season_id;
        $relic->name = $request->relic_name;
        $relic->desc = $request->relic_desc;
        $relic->active = $request->has('active')? true: false;
        $relic->complexity = (int) $request->complexity;
        $relic->clues = $this->allotGameToClueService->allot($request);
        $relic->fees = (float)$request->fees;
        $relic->save();

        return response()->json(['status'=> true, 'message'=> 'Relic updated! Please wait we are redirecting you.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $relic = Relic::find($id);
        Storage::disk($this->disk)->delete([
            'seasons/'.$relic->season_id.'/'.$relic->active_icon,
            'seasons/'.$relic->season_id.'/'.$relic->inactive_icon
        ]);
        $relic->delete();

        return response()->json(['status'=> true, 'message'=> 'Season deleted successfully.']);
    }

    public function clueHTML(Request $request)
    {
        if($request->ajax()) {
            return view('admin.seasons.relics.clues.create')->with(['index'=> $request->index]);
        }
        throw new Exception("You are not allow make this request");
    }

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $seasons = Relic::when($search != '', function($query) use ($search) {
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
        $filterCount = Relic::when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('name','like','%'.$search.'%')
            ->orWhere('active', $active)
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->count();

        return DataTables::of($seasons)
        ->addIndexColumn()
        ->editColumn('active', function($relic){
            return ($relic->active)? "Active": "Inactive";
        })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('season', function($relic){
            return "<a href=".$relic->season->path().">{$relic->season->name}</a>";
        })
        ->addColumn('action', function($relic){
            if(auth()->user()->hasPermissionTo('Edit Seasonal Hunt')){
                $html = '<a href="'.route('admin.relics.edit',$relic->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }

            if(auth()->user()->hasPermissionTo('Delete Seasonal Hunt')){
                $html .= ' <a href="'.route('admin.relics.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
            }
            return $html;
        })
        ->rawColumns(['action', 'season'])
        ->setTotalRecords(Relic::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
