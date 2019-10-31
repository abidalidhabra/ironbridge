<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class SeasonController extends Controller
{

    private $disk = 'public';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.seasons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.seasons.create');
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
            'season_name'=> 'required|min:5',
            'season_slug'=> 'required|min:5|unique:seasons,slug',
            'active'=> 'nullable|in:true',
            'icon'=> 'required|image',
            // 'active_icon'=> 'required|image',
            // 'inactive_icon'=> 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $season = Season::create([ 
            'name'=> $request->season_name,
            'slug'=> $request->season_slug,
            'active'=> $request->has('active')? true: false,
            'icon'=> $request->icon->hashName(),
            // 'active_icon'=> $request->active_icon->hashName(),
            // 'inactive_icon'=> $request->inactive_icon->hashName(),
        ]);

        $request->icon->store('seasons/'.$season->id, $this->disk);
        // $request->active_icon->store('seasons/'.$season->id, $this->disk);
        // $request->inactive_icon->store('seasons/'.$season->id, $this->disk);

        return response()->json(['status'=> true, 'message'=> 'Season added! Please wait we are redirecting you.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        return view('admin.seasons.show', ['season'=> Season::where('slug', $slug)->first()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.seasons.edit', ['season'=> Season::find($id)]);
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
            'season_name'=> 'required|min:5',
            'season_slug'=> ['required', 'min:5', Rule::unique('seasons', 'slug')->ignore($id, '_id') ],
            'active'=> 'nullable|in:true',
            'icon'=> 'nullable|image',
            // 'active_icon'=> 'nullable|image',
            // 'inactive_icon'=> 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->messages()->first()], 422);
        }

        $season = Season::find($id);

        if ($request->hasFile('icon')) {
            Storage::disk($this->disk)->delete('seasons/'.$season->id.'/'.$season->icon);
            $request->icon->store('seasons/'.$season->id, $this->disk);
            $season->icon = $request->icon->hashName();
        }

        // if ($request->hasFile('active_icon')) {
        //     Storage::disk($this->disk)->delete('seasons/'.$season->id.'/'.$season->active_icon);
        //     $request->active_icon->store('seasons/'.$season->id, $this->disk);
        //     $season->active_icon = $request->active_icon->hashName();
        // }

        // if ($request->hasFile('inactive_icon')) {
        //     Storage::disk($this->disk)->delete('seasons/'.$season->id.'/'.$season->inactive_icon);
        //     $request->inactive_icon->store('seasons/'.$season->id, $this->disk);
        //     $season->inactive_icon = $request->inactive_icon->hashName();
        // }

        $season->name = $request->season_name;
        $season->desc = $request->season_desc;
        $season->active = $request->has('active')? true: false;
        $season->save();

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
        Storage::disk($this->disk)->deleteDirectory('seasons/'.$season->id);
        $season->delete();

        return response()->json(['status'=> true, 'message'=> 'Season deleted successfully.']);
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
        ->editColumn('name', function($season){
            return "<a href=".$season->path().">$season->name</a>";
        })
        ->editColumn('active', function($season){
            return ($season->active)? "Active": "Inactive";
        })
        ->editColumn('created_at', function($season){
            return $season->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('active', function($season){
            return ($season->active)? "Active": "Inactive";
        })
        ->addColumn('action', function($season){
            if(auth()->user()->hasPermissionTo('Edit Seasonal Hunt')){
                $html = '<a href="'.route('admin.seasons.edit',$season->id).'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }

            if(auth()->user()->hasPermissionTo('Delete Seasonal Hunt')){
                $html .= ' <a href="'.route('admin.seasons.destroy',$season->id).'" data-action="delete" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash iconsetaddbox"></i></a>';
            }
            return $html;
        })
        ->rawColumns(['action', 'name'])
        ->setTotalRecords(Season::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }
}
