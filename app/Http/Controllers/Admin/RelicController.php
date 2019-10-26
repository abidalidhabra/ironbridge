<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Relic;
use App\Models\v2\Season;
use App\Services\Hunt\SponserHunt\AllotGameToClueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RelicController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($season_slug)
    {
        return view('admin.seasons.relics.create', ['season'=> Season::where('slug', $season_slug)->first()]);
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
            'active_icon'=> 'required|image',
            'inactive_icon'=> 'required|image',
            'complexity'=> 'required|numeric|integer:min:1',
            'clues.*.name'=> 'required|string',
            'clues.*.desc'=> 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $season = Season::where('slug', $season_slug)->select('_id', 'name')->first();
        
        $request->active_icon->store('seasons/'.$season->id, $this->disk);
        $request->inactive_icon->store('seasons/'.$season->id, $this->disk);
        
        $season->relics()->create([
            'name'=> $request->relic_name,
            'desc'=> $request->relic_desc,
            'active'=> $request->has('active')? true: false,
            'active_icon'=> $request->active_icon->hashName(),
            'inactive_icon'=> $request->inactive_icon->hashName(),
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
        return view('admin.seasons.relics.edit', ['relic'=> Relic::find($id)]);
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
            'active_icon'=> 'nullable|image',
            'inactive_icon'=> 'nullable|image',
            'complexity'=> 'required|numeric|integer:min:1',
            'clues.*.name'=> 'required|string',
            'clues.*.desc'=> 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $relic = Relic::find($id);

        if ($request->hasFile('active_icon')) {
            Storage::disk($this->disk)->delete('seasons/'.$relic->season_id.'/'.$relic->active_icon);
            $request->active_icon->store('seasons/'.$relic->season_id, $this->disk);
            $relic->active_icon = $request->active_icon->hashName();
        }

        if ($request->hasFile('inactive_icon')) {
            Storage::disk($this->disk)->delete('seasons/'.$relic->season_id.'/'.$relic->inactive_icon);
            $request->inactive_icon->store('seasons/'.$relic->season_id, $this->disk);
            $relic->inactive_icon = $request->inactive_icon->hashName();
        }

        $relic->name = $request->relic_name;
        $relic->desc = $request->relic_desc;
        $relic->active = $request->has('active')? true: false;
        $relic->complexity = (int) $request->complexity;
        $relic->clues = $this->allotGameToClueService->allot($request);
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
}
