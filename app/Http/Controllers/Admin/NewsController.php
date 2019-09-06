<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\News;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use Auth;
use File;
use Image;
use Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.news.index');
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
        $validator = Validator::make($request->all(),[
            'subject'     => 'required',
            'description' => 'required',
            'valid_till'  => 'required',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        
        $data = $request->all();
        $data['valid_till'] = date('Y-m-d H:i:s',strtotime($request->get('valid_till')));

        if($request->hasfile('image')){
            $image = $request->file('image');
            if(!File::exists(storage_path('app/public/news/'))){
                $directoryPath = storage_path('app/public/news/');
                File::makeDirectory($directoryPath,0755,true);
            }            
            $extension = $image->getClientOriginalExtension();
            $img = Image::make($image);
            $height = $img->height();
            $width = $img->width();
            
            $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
            $img->save(storage_path('app/public/news/'.$imageUniqueName));
            $data['image'] = $imageUniqueName;
            
        }
        
        News::create($data);
        
        return response()->json([
            'status' => true,
            'message'=>'News has been added successfully.',
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
    public function update(Request $request,$id)
    {
        $data = [
            'subject'     => $request->get('subject'),
            'description' => $request->get('description'),
            'valid_till'  => $request->get('valid_till'),
            'news_id'     => $request->get('news_id'),
        ];

        $validator = Validator::make($data, [
            'subject' => 'required',
            'description' => 'required',
            'valid_till' => 'required',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        $data['valid_till'] = date('Y-m-d H:i:s',strtotime($request->get('valid_till')));

        $news = News::where('_id',$id)->first();

        if($request->hasfile('image')){
            $image = $request->file('image');

            if ($news->image != '') {
                $news1 = \DB::table('news')->where('_id',$id)->first();
                Storage::disk('public')->delete('news/'.$news1['image']);    
            }

            if(!File::exists(storage_path('app/public/news/'))){
                $directoryPath = storage_path('app/public/news/');
                File::makeDirectory($directoryPath,0755,true);
            }            
            $extension = $image->getClientOriginalExtension();
            $img = Image::make($image);
            $height = $img->height();
            $width = $img->width();
            
            $imageUniqueName = uniqid(uniqid(true).'_').'.'.$extension;
            $img->save(storage_path('app/public/news/'.$imageUniqueName));
            $data['image'] = $imageUniqueName;
            
        }

        $news->update($data);

        return response()->json([
            'status' => true,
            'message'=>'News has been updated successfully.',
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
        News::where('_id', $id)->delete();
        return response()->json([
            'status' => true,
            'message'=>'News has been deleted successfully.',
        ]);
    }

    public function getNewsList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $news = News::select('subject','description','valid_till','image');
        $admin = Auth::user();

        if($search != ''){
            $news->where(function($query) use ($search){
                $query->where('subject','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%')
                ->orWhere('valid_till','like','%'.$search.'%');
            });
        }

        $news = $news->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = News::count();
        if($search != ''){
            $count = News::where(function($query) use ($search){
                $query->where('subject','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%')
                ->orWhere('valid_till','like','%'.$search.'%');
            })->count();
        }

        return DataTables::of($news)
        ->addIndexColumn()
        ->editColumn('valid_till', function($news){
            return Carbon::parse($news->valid_till)->format('d-M-Y');
        })
        ->editColumn('image', function($news){
            if ($news->image!="") {
                return '<img src="'.$news->image.'" style="width: 77px;">';
            }
            return '';
        })
        ->addColumn('action', function($news) use ($admin){
            $date = Carbon::parse($news->valid_till)->format('d-m-Y');
            
            $data = '';
            if($admin->hasPermissionTo('Edit News')){
                $data .=  '<a href="javascript:void(0)" class="edit_company" data-action="edit" data-id="'.$news->id.'" data-subject="'.$news->subject.'" data-valid_till="'.$date.'" data-description="'.$news->description.'" data-image="'.$news->image.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }
            if($admin->hasPermissionTo('Delete News')){
                $data .=  '<a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$news->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
                </a>';
            }

            return $data;
        })
        ->rawColumns(['action','image'])
        ->order(function ($query) {
            if (request()->has('created_at')) {
                $query->orderBy('created_at', 'DESC');
            }

        })
        ->setTotalRecords($count)
        ->setFilteredRecords($count)
        ->skipPaging()
        ->make(true);
    }
}
