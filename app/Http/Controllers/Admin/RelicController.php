<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Relic;
use App\Models\v2\Season;
use App\Models\v2\Loot;
use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use App\Services\Hunt\SponserHunt\AllotGameToClueService;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

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
        return view('admin.relics.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.relics.create');
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
            'name'          => 'required',
            'icon'          => 'required|image',
            'complexity'    => 'required|numeric',
            'pieces'        => 'required|numeric',
            'number'        => 'required|numeric|integer|unique:relics,number',
            'status'        => 'required|in:active,inactive',
            'minigame_xp'   => 'required|numeric',
            'hunt_xp'      => 'required|numeric',
            // 'pieces.*.image'=> 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(),'status'=>false]);
        }

        if(Relic::where('number',(int)$request->number)->first()){
            return response()->json(['message'=> 'Relic number already exists','status'=>false]);    
        }
        $request->icon->store('relics/'.$request->complexity, $this->disk);
        // $request->active_icon->store('seasons/'.$season->id, $this->disk);
        // $request->inactive_icon->store('seasons/'.$season->id, $this->disk);
        
        $miniGames = (new ParticipationInRandomHuntRepository)->randomizeGames(1);
        Relic::create([
            'name'=> $request->name,
            'icon'=> $request->icon->hashName(),
            'complexity'=> (int)$request->complexity,
            'pieces'=> (int)$request->pieces,
            // 'game_id'=> $miniGames[0]->id,
            // 'game_variation_id'=> $miniGames[0]->game_variation()->limit(1)->first()->id,
            'pieces'=> (int)$request->pieces,
            'number'=> (int)$request->number,
            'active'=> ($request->status=='active')?true:false,
            'completion_xp' => (object)['clue'=>(int)$request->minigame_xp,'treasure'=>(int)$request->hunt_xp],
            //'pieces'=> $this->allotGameToClueService->allot($request),
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
        $relic = Relic::where('_id',$id)->with('loot_info')->first();
        return view('admin.relics.show', ['relic'=> $relic]);
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
        return view('admin.relics.edit', ['relic'=> $relic]);
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
            // 'icon'=> 'nullable|image',
            'name'          => 'required',
            'complexity'    => 'required|numeric|integer:min:1',
            'pieces'        => 'required|numeric',
            'number'        => 'required|numeric|integer',
            'status'        => 'required|in:active,inactive',
            'minigame_xp'   => 'required|numeric',
            'hunt_xp'       => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first(),'status'=>false]);
        }

        if(Relic::where('number',(int)$request->number)->where('_id','!=',$id)->first()){
            return response()->json(['message'=> 'Relic number already used','status'=>false]);    
        }
        
        $relic = Relic::find($id);

        
        $dbRelic = DB::table('relics')->where('_id',$id)->first(); 

        if ($request->hasFile('icon')) {
            $request->icon->store('relics/'.$request->complexity, $this->disk);
            $relic->icon = $request->icon->hashName();
            Storage::disk($this->disk)->delete('relics/'.$dbRelic['complexity'].'/'.$dbRelic['icon']);
        } else {
            if ($relic->complexity != $request->complexity) {
                Storage::disk('public')->move('/relics/'.$dbRelic['complexity'].'/'.$dbRelic['icon'], '/relics/'.$request->complexity.'/'.$dbRelic['icon']);
            }
        }



        //$request['status'] = "edit";
        $request['id'] = $id;

        $relic->complexity = (int) $request->complexity;
        $relic->name = $request->name;
        $relic->pieces = (int) $request->pieces;
        $relic->number = (int) $request->number;
        $relic->active = ($request->status=='active')?true:false;
        $relic->completion_xp = (object)['clue'=>(int)$request->minigame_xp,'treasure'=>(int)$request->hunt_xp];        
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
        // $relic = Relic::find($id);
        $relic = DB::table('relics')->where('_id',$id)->first();
        if (isset($relic['loot_tables'])) {
            $loot = Loot::whereIn('_id',$relic['loot_tables'])->first();
            if ($loot) {
                return response()->json(['message'=>'This relic table can not be delete.'],422);
            }
        }
        Storage::disk($this->disk)->delete('relics/'.$relic['complexity'].'/'.$relic['icon']);

        /*foreach ($relic['pieces'] as $key => $value) {
            Storage::disk($this->disk)->delete('relics/'.$relic['complexity'].'/'.$value['image']);
        }*/

        Relic::find($id)->delete();

        return response()->json(['status'=> true, 'message'=> 'Relic deleted successfully.']);
    }

    public function clueHTML(Request $request)
    {
        if($request->ajax()) {
            return view('admin.relics.clues.create')->with(['index'=> $request->index]);
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
            ->orWhere('complexity','like','%'.$search.'%')
            ->orWhere('loot_table_number','like','%'.$search.'%')
            ->orWhere('name','like','%'.$search.'%')
            ->orWhere('pieces','like','%'.$search.'%')
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->orderBy('number','asc')
        ->skip($skip)
        ->take($take)
        ->get();
        
        /** Filter Result Total Count  **/
        $filterCount = Relic::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('complexity','like','%'.$search.'%')
            ->orWhere('loot_table_number','like','%'.$search.'%')
            ->orWhere('name','like','%'.$search.'%')
            ->orWhere('pieces','like','%'.$search.'%')
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->count();
                
        $admin = Auth::User();
        return DataTables::of($seasons)
        ->addIndexColumn()
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('icon', function($relic){
            return '<img src="'.$relic->icon.'" style="width: 70px;">';
        })
        ->editColumn('active', function($relic){
            return ($relic->active==true)?'Active':'InActive';
        })
        ->editColumn('name', function($relic){
            return ($relic->name)?$relic->name:'-';
        })
        ->addColumn('hunt_xp', function($relic){
            return ($relic->completion_xp)?$relic->completion_xp['treasure']:'-';
        })
        ->addColumn('minigame_xp', function($relic){
            return ($relic->completion_xp)?$relic->completion_xp['clue']:'-';
        })
        ->addColumn('action', function($relic) use($admin){
                $html = '';
                if($admin->hasPermissionTo('Edit Relics')){
                    $html .= '<a href="'.route('admin.relics.edit',$relic->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
                }

                if($admin->hasPermissionTo('Delete Relics')){
                    $html .= ' <a href="'.route('admin.relics.destroy',$relic->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
                }

                $html .= ' <a href="'.route('admin.relics.show',$relic->id).'" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>';
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->setTotalRecords(Relic::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }

    public function removePieces(Request $request){
        // $relic = Relic::where('_id', $request->id)->first();
        $relic =  DB::table('relics')->where('_id', $request->id)->first();
        
        Storage::disk($this->disk)->delete('relics/'.$relic['complexity'].'/'.$relic['pieces'][$request->pieces_id-1]['image']);
        
        Relic::where('_id', $request->id)->pull('pieces', ['id' => (int)$request->pieces_id]);

        return response()->json(['status'=> true, 'message'=> 'Image deleted successfully.']);
    }
}
