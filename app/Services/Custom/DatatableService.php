<?php

namespace App\Services\Custom;

class DatatableService
{

	public $start;
	public $length;
	public $model;

	public function __construct(int $start, int $length)
	{
		$this->skip = (int)$request->start;
		$this->take = (int)$request->length;
	}

	

	public function FunctionName()
	{
		

	$huntMode = $request->get('hunt_mode');
        
        if ($huntMode == 'challenge' || $huntMode == 'normal') {
            $huntUser = HuntUser::select('hunt_id','user_id','status','created_at','hunt_complexity_id','hunt_mode')
                            ->with([
                                    'Hunt:_id,name,fees',
                                    'user:_id,first_name,last_name',
                                    'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                    'hunt_complexities:_id,distance'
                                ])
                            ->where('hunt_mode' , $huntMode)
                            ->orderBy('created_at','DESC')
                            ->skip($skip)
                            ->take($take)
                            ->get();
            $count = HuntUser::where('hunt_mode' , $huntMode)
                            ->count();
        } else {
            $huntUser = HuntUser::select('hunt_id','user_id','status','created_at','hunt_complexity_id')
                        ->with([
                                'Hunt:_id,name,fees',
                                'user:_id,first_name,last_name',
                                'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                'hunt_complexities:_id,distance'
                            ])
                        ->orderBy('created_at','DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get();
            $count = HuntUser::count();
        }
        

        return DataTables::of($huntUser)
        ->addIndexColumn()
        ->addColumn('hunt_name', function($user){
            return $user->hunt->name;
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('username', function($user){
            return $user->user->first_name.' '.$user->user->last_name;
        })
        ->addColumn('fees', function($user){
            return $user->hunt->fees;
        })
        ->addColumn('clue_progress', function($user){
            $completedClue = $user->hunt_user_details()->where('status','completed')->count();
            $totalClue = $user->hunt_user_details()->count();
            
            return $completedClue.'/'.$totalClue;
        })
        ->addColumn('distance_progress', function($user){
                    $completedClues = 0;
                    $completedDist  = 0;
                    $totalClues = $user->hunt_user_details()->count();
                    $completedClues = $user->hunt_user_details()->where('status','completed')->count();
                    $totalDistance = $user->hunt_complexities->distance;
                    $completedDist = (($user->hunt_complexities->distance / $totalClues) * $completedClues);
                    
                    
                    return $completedDist.' / '.$totalDistance;
        })
        ->addColumn('view', function($user){
            return '<a href="'.route('admin.userHuntDetails',$user->id).'" >More</a>';
        })
        ->rawColumns(['view'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($count)
        ->skipPaging()
        ->make(true);
	}
}