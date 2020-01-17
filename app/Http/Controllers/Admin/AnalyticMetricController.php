<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\AdminPasswordSetLink;
use App\Models\v1\News;
use App\Models\v1\User;
use App\Models\v2\Hunt;
use App\Models\v2\HuntUser;
use App\Models\v2\Event;
use App\Models\v2\EventsUser;
use Carbon\Carbon;
use Validator;
use Auth;
use Hash;
use App\Models\v1\WidgetItem;
use App\Models\v2\PlanPurchase;
use App\Models\v2\HuntComplexity;
use App\Models\v2\HuntUserDetail;
use App\Models\v2\Relic;
use Yajra\DataTables\DataTables;

class AnalyticMetricController extends Controller
{
    public function analyticsMetrics(Request $request){
        $user = User::select('id','first_name','last_name','gender','agent_status','relics','tutorials','created_at')
                    ->get();

        $huntUser = HuntUser::whereHas('user')->get();

        $data['user_start_date'] = $user->first()->created_at;
        $data['user_end_date'] = $user->last()->created_at;
        $data['hunt_user_start_date'] = $huntUser->first()->created_at;
        $data['hunt_user_end_date'] = $huntUser->last()->created_at;
        
        
        /* USER */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_user'] = $user->count();
        $data['per_male'] = number_format(($data['total_male']/$data['total_user'])*100,2).'%';
        $data['per_female'] = number_format(($data['total_female']/$data['total_user'])*100,2).'%';
        // $data['highest_xp'] = $user->sortByDesc('agent_status.xp')->take(5)->toArray();
        $data['highest_xp'] = User::select('id','first_name','last_name','agent_status','created_at')
                                    ->take(5)
                                    ->orderBy('agent_status.xp','desc')
                                    ->get()
                                    ->toArray();
        $data['highest_relic'] = User::select('id','first_name','last_name','relics','created_at')
                                      ->orderBy('relics','desc')
                                      ->take(5)
                                      ->get()
                                      ->toArray();
        /* END USER */

        /*Tutorials*/
        $tutorials = $user->first()->tutorials->toArray();
        foreach ($tutorials as $key => $value) {
            $data['tutorials'][$key] = number_format(($user->where('tutorials.'.$key,'!=',null)->count()/$data['total_user'])*100,2).'%';
        }
        /* END Tutorials */

        /* HUNTS */
        $data['played_random_hunts'] = number_format(($huntUser->groupBy('user_id')->count()/$data['total_user'])*100,2).'%';
        
        $data['completed_random_hunts'] = number_format(($huntUser->where('status','completed')->where('relic_reference_id','!=',null)->groupBy('user_id')->count()/$huntUser->count())*100,2).'%';

        $data['played_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->groupBy('user_id')->count()/$data['total_user'])*100,2).'%';
        
        $data['completed_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->where('status','completed')->groupBy('user_id')->count()/$huntUser->count())*100,2).'%';
        
        $data['average_of_random_hunts'] = number_format(($huntUser->count()/$data['total_user']));
        
        $data['average_of_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->count()/$data['total_user']));
        /* END HUNTS */

        // return $data;
        return view('admin.analytics.analytics_metrics',compact('data'));   
    }


    public function getUserDateFilter(Request $request){
        $date = explode('-', $request->get('user_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');
        $user = User::select('id','first_name','last_name','gender','agent_status','relics','tutorials','created_at')
                      ->whereBetween('created_at', [$startAt,$endAt])
                      ->get();

        /* GET USER DATA  */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_user'] = $user->count();
        $data['per_male'] = number_format(($data['total_male']/$data['total_user'])*100,2).'%';
        $data['per_female'] = number_format(($data['total_female']/$data['total_user'])*100,2).'%';
        $data['highest_xp'] = User::select('id','first_name','last_name','agent_status','created_at')
                                    ->whereBetween('created_at', [$startAt,$endAt])
                                    ->take(5)
                                    ->orderBy('agent_status.xp','desc')
                                    ->get()
                                    ->toArray();
        $data['highest_relic'] = User::select('id','first_name','last_name','relics','created_at')
                                      ->whereBetween('created_at', [$startAt,$endAt])
                                      ->orderBy('relics','desc')
                                      ->take(5)
                                      ->get()
                                      ->toArray();
        /* ENND USER DATA  */

        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }



    public function getTutorialDateFilter(Request $request){
        $date = explode('-', $request->get('tutorial_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');

        $user = User::select('id','first_name','last_name','gender','agent_status','relics','tutorials','created_at')
                      ->whereBetween('created_at', [$startAt,$endAt])
                      ->get();

        /*Tutorials*/
        $tutorials = $user->first()->tutorials->toArray();
        $data['total_user'] = $user->count();
        foreach ($tutorials as $key => $value) {
            $data['tutorials'][ucfirst(str_replace('_', ' ', $key))] = number_format(($user->where('tutorials.'.$key,'!=',null)->count()/$data['total_user'])*100,2).'%';
        }
        /* END Tutorials */

        $data['tutorials1'] = array_slice($data['tutorials'],0,6);
        $data['tutorials2'] = array_slice($data['tutorials'],7,14);


        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }


    public function getHuntDateFilter(Request $request){
        $date = explode('-', $request->get('hunt_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');
        $currentDate = Carbon::now();


        $huntUser = HuntUser::whereHas('user')
                            ->whereBetween('created_at', [$startAt,$endAt])
                            ->get();

        $user = User::get();

        /* HUNTS */
        $data['completed_random_hunts'] = number_format(($huntUser->where('status','completed')->where('relic_reference_id','!=',null)->count()/$huntUser->count())*100,2).'%';

        $data['completed_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->where('status','completed')->count()/$huntUser->count())*100,2).'%';

        $data['played_random_hunts'] = number_format(($huntUser->groupBy('user_id')->count()/$user->count())*100,2).'%';
        
        $data['completed_random_hunts'] = number_format(($huntUser->where('status','completed')->where('relic_reference_id','!=',null)->groupBy('user_id')->count()/$huntUser->count())*100,2).'%';

        $data['played_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->groupBy('user_id')->count()/$user->count())*100,2).'%';
        
        $data['completed_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->where('status','completed')->groupBy('user_id')->count()/$huntUser->count())*100,2).'%';
        
        $data['average_of_random_hunts'] = number_format(($huntUser->count()/$user->count()));
        
        $data['average_of_relic_hunts'] = number_format(($huntUser->where('relic_id','!=',null)->count()/$user->count()));
        /* END HUNTS */

        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }

    public function XPList(Request $request){
        return view('admin.analytics.xp_list');   
    }

    public function getXPList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $users = User::select('id','first_name','last_name', 'agent_status','created_at')
                        ->when($search != '', function($query) use ($search) {
                            $query->where('first_name','like','%'.$search.'%')
                                    ->orWhere('last_name','like','%'.$search.'%')
                                    ->orWhere('agent_status.xp','like','%'.$search.'%');
                        })
                        ->orderBy('agent_status.xp','desc')
                        ->skip($skip)
                        ->take($take)
                        ->get();

        $filterCount = User::select('id','first_name','last_name', 'agent_status','created_at')
                            ->when($search != '', function($query) use ($search) {
                                $query->where('first_name','like','%'.$search.'%')
                                ->orWhere('last_name','like','%'.$search.'%')
                                ->orWhere('agent_status.xp','like','%'.$search.'%');
                            })
                            ->count();

        $admin = auth()->user();
        return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function($user){
                    return '<a href="'. route("admin.accountInfo",$user->_id) .'">'.$user->first_name.' '.$user->last_name.'</a>';
                })
                ->addColumn('xp', function($user){
                    return $user->agent_status['xp'];
                })
                ->rawColumns(['name', 'icon'])
                ->setTotalRecords(User::count())
                ->setFilteredRecords($filterCount)
                ->skipPaging()
                ->make(true);
    }


    public function relicsList(Request $request){
        return view('admin.analytics.relics_list');
    }

    public function getRelicsList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $users = User::select('id','first_name','last_name', 'agent_status','relics','created_at')
                        ->when($search != '', function($query) use ($search) {
                            $query->where('first_name','like','%'.$search.'%')
                                    ->orWhere('last_name','like','%'.$search.'%');
                        })
                        ->orderBy('relics','desc')
                        ->skip($skip)
                        ->take($take)
                        ->get();

        $filterCount = User::select('id','first_name','last_name', 'agent_status','created_at')
                            ->when($search != '', function($query) use ($search) {
                                $query->where('first_name','like','%'.$search.'%')
                                ->orWhere('last_name','like','%'.$search.'%');
                            })
                            ->count();

        $admin = auth()->user();
        return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function($user){
                    return '<a href="'. route("admin.accountInfo",$user->_id) .'">'.$user->first_name.' '.$user->last_name.'</a>';
                })
                ->addColumn('relics', function($user){
                    return $user->relics->count();
                })
                ->rawColumns(['name', 'icon'])
                ->setTotalRecords(User::count())
                ->setFilteredRecords($filterCount)
                ->skipPaging()
                ->make(true);
    }
}
