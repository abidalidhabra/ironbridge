<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use App\Models\v1\User;
use App\Models\v1\WidgetItem;
use App\Models\v2\EventsUser;
use App\Models\v2\HuntUser;
use App\Models\v2\HuntUserDetail;
use App\Models\v2\PlanPurchase;
use App\Models\v2\PracticeGameUser;
use App\Models\v2\Relic;
use App\Models\v3\City;
use App\Repositories\RelicRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Validator;
use Yajra\DataTables\EloquentDataTable;
use Yajra\Datatables\Datatables;
//use App\Services\Event\EventService;


class UserController extends Controller
{
    public function index()
    {
     //     (new EventService)->participate();
    	return view('admin.user.userList');
    }

    //GET USER
    public function getUsers(Request $request)
    {	
        /*$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

    	$user = User::select('first_name','last_name','username', 'email', 'mobile_no', 'gold_balance','created_at','skeleton_keys','device_info','guest_id');
        if($search != ''){
            $user->where(function($query) use ($search){
                $query->where('first_name','like','%'.$search.'%')
                ->orWhere('last_name','like','%'.$search.'%')
                ->orWhere('username','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('mobile_no','like','%'.$search.'%')
                ->orWhere('dob','like','%'.$search.'%')
                ->orWhere('created_at','like','%'.$search.'%');
            });
        }
        $user = $user->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = User::count();
        if($search != ''){
            $count = User::where(function($query) use ($search){
                $query->where('first_name','like','%'.$search.'%')
                ->orWhere('last_name','like','%'.$search.'%')
                ->orWhere('username','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('mobile_no','like','%'.$search.'%')
                ->orWhere('dob','like','%'.$search.'%')
                ->orWhere('created_at','like','%'.$search.'%');
            })->count();
        }*/

        $columns = array( 
                            0 => '_id', 
                            1 => 'created_at',
                            2 => 'first_name',
                            3 => 'email',
                            4 => 'username',
                            5 => 'gold_balance',
                            6 => 'available_skeleton_keys',
                            // 7 => 'device.type',
                        );

        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $order = $columns[$request->input('order.0.column')];
        $search = $request->get('search')['value'];
        $status = $request->status;
        
        if(!empty($request->input('search.value')) || $request->input('order.0.column') != 0){
            $dir = $request->input('order.0.dir');
        } else {
            $dir = 'desc';
        }

        $user = User::select('first_name','last_name','username', 'email', 'mobile_no', 'gold_balance','created_at','skeleton_keys','device_info','guest_id')
                    ->when($search != '', function($query) use ($search) {
                                $active = ($search == 'true' || $search == 'Active')? true: false;
                                $query->orWhere('first_name', 'LIKE',"%{$search}%")
                                       ->orWhere('last_name', 'LIKE',"%{$search}%")
                                       ->orWhere('email', 'LIKE',"%{$search}%")
                                       ->orWhere('username', 'LIKE',"%{$search}%")
                                       ->orWhere('gold_balance', 'LIKE',"%{$search}%")
                                       ->orWhere('created_at', 'LIKE',"%{$search}%");
                            })
                    ->orderBy($order,$dir)
                    ->skip($skip)
                    ->take($take)
                    ->get();
        $filterCount = User::when($search != '', function($query) use ($search) {
                                $active = ($search == 'true' || $search == 'Active')? true: false;
                                $query->orWhere('first_name', 'LIKE',"%{$search}%")
                                       ->orWhere('last_name', 'LIKE',"%{$search}%")
                                       ->orWhere('email', 'LIKE',"%{$search}%")
                                       ->orWhere('username', 'LIKE',"%{$search}%")
                                       ->orWhere('gold_balance', 'LIKE',"%{$search}%")
                                       ->orWhere('created_at', 'LIKE',"%{$search}%");
                            })->count();

        return DataTables::of($user)
        ->addIndexColumn()
        ->addColumn('name', function($user){
            if ($user->first_name) {
                return '<a href="'.route('admin.accountInfo',$user->id).'">'.$user->first_name.' '.$user->last_name.'</a>';
            }else{
                return '<a href="'.route('admin.accountInfo',$user->id).'">'.$user->guest_id.'</a>';
            }
            // return '-';
        })
        ->editColumn('username', function($user){
            if ($user->username) {
                return $user->username;
            } 
            return '-';
        })
        ->editColumn('email', function($user){
            if ($user->email) {
                return $user->email;
            } 
            return '-';
        })
        ->addColumn('device', function($user){
            if($user->device_info['type']){
                if($user->device_info['type'] == 'android'){
                    return  'Android';
                } else {
                    return  'iOS';
                }
            } else {
                    return  '-';
            }
        })
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->editColumn('skeleton_keys', function($user){
            $skeleton_keys = collect($user->skeleton_keys)->where('used_at',null)->count();

            return $skeleton_keys;
        })
        
        ->addColumn('action', function($user){
            return '<a href="'.route('admin.accountInfo',$user->id).'"><i class="fa fa-eye iconsetaddbox"></i></a>';
        })
        ->rawColumns(['name','action'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        ->setTotalRecords($filterCount)
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }


    //USER Participated index 
    public function usersParticipatedList(Request $request){
        return view('admin.user.usersParticipatedList');
    }

    public function getUsertParticipatedList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
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

    //USER HUNT DETAILS
    public function userHuntDetails($id){
        $huntUserDetail = HuntUserDetail::select('hunt_user_id','game_id','game_variation_id','revealed_at','finished_in','status','skipped_at','failures_at')      
                                        ->with('hunt_user:id,user_id')
                                        ->with(['game:_id,name','game_variation:_id,variation_name'])
                                        ->where('hunt_user_id',$id)
                                        ->get();
        $id = $huntUserDetail[0]->hunt_user->user_id;
        
        return view('admin.user.userHuntDetails',compact('huntUserDetail','id'));
    }



    //ACCOUNT INFO
    public function accountInfo($id){

        $user = User::with([
                            'hunt_user_v1:user_id,hunt_id,hunt_complexity_id,status,hunt_mode,complexity',
                            'hunt_user_v1.hunt:_id,fees',
                        ])
                    ->where('_id',$id)
                    ->first(); 
        
        $data['usedGold'] = 0;
        $data['currentGold'] = 0;
        $data['totalGold'] = 0;
        $data['skeleton'] = 0;
        
        $data['widgetsIdSelected'] = [];
        foreach ($user->widgets as $key => $value) {
            $data['widgetsIdSelected'][$value['id']] = ($value['selected'])?true:false;
        }

        $widgetsId =  collect($user->widgets)->pluck('id');           
        if($user){
            $data['usedGold'] = $user->hunt_user_v1->where('hunt_mode','challenge')->pluck('hunt.fees')->sum();
            $data['currentGold'] = $user->gold_balance;
            $data['totalGold'] = $data['usedGold'] + $data['currentGold'];
            if($user->skeleton_keys){
                $userSkKeys = collect($user->skeleton_keys);
                $availSkKeys = $userSkKeys->where('used_at', null)->count();
                $data['skeleton'] = $availSkKeys;
            }
        }

        /*RELIC*/
        $relics = (new RelicRepository)->getModel()
                ->active()
                ->select('_id', 'icon','name', 'complexity','pieces')
                ->get()
                ->map(function($relic) use ($user) {
                    $relic->acquired = $user->relics->where('id', $relic->_id)->first();
                    // $relic->collected_pieces = $relic->hunt_users_reference()->where(['status'=> 'completed', 'user_id'=> $user->id])->count();
                    $relic->collected_pieces = 0;
                    return $relic; 
                });

        $cities = City::get();
                
        return view('admin.user.accountInfo',compact('id','user','data','relics','cities'));
    }

    //AVTAR ITEMS
    public function avatarItems($id){

        $user = User::where('_id',$id)->first(); 
            
        $data['widgetsIdSelected'] = [];
        foreach ($user->widgets as $key => $value) {
            $data['widgetsIdSelected'][$value['id']] = ($value['selected'])?true:false;
        }

        $widgetsId =  collect($user->widgets)->pluck('id');
        
        $data['widget'] = WidgetItem::select('_id','widget_name','item_name','avatar_id','gold_price','widget_category')
                                ->whereIn('_id',$widgetsId)
                                ->orderBy('widget_name','desc')
                                ->get()
                                ->groupBy('widget_name');

        return view('admin.user.avatarItems',compact('id','user','data'));
    }

    //user treasureHunts
    public function treasureHunts($id){
        return view('admin.user.treasureHunts',compact('id'));
    }

    //LIST TREASUREHUNTS
    public function getTreasureHunts(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        
        $userId = $request->get('user_id');
        $status = $request->get('status');
        
        $status_value = ['participated', 'paused', 'running', 'completed','terminated'];
        if ($status == 'completed') {
            $status_value = ['completed'];
        } else if ($status == 'progress') {
            $status_value = ['participated', 'paused', 'running'];
        } else if ($status == 'terminated') {
            $status_value = ['terminated'];
        }


        $huntUser = HuntUser::with([
                                'hunt_user_details:_id,hunt_user_id,status,finished_in',
                                'relic:_id,name',
                                // 'relic_reference:_id,name',
                            ])
                            ->where('user_id',$userId)
                            ->whereIn('status',$status_value)
                            ->where(function($query) use ($request){
                                if ($request->type == 'random') {
                                    $query->whereNull('relic_id');
                                } elseif ($request->type == 'relic') {
                                    $query->whereNotNull('relic_id');
                                }
                            })
                            ->orderBy('created_at','DESC')
                            ->get();

        return DataTables::of($huntUser)
        ->addIndexColumn()
        ->editColumn('created_at', function($user){
            return Carbon::parse($user->created_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('status', function($user){
            if ($user->status == 'participated') {
                return '<label class="label label-primary">Not Started</label>';
            } elseif($user->status == 'paused' || $user->status == 'running') {
                return '<label class="label label-primary">In Progress</label>';
            } else if($user->status == 'completed'){
                return '<label class="label label-success">Completed</label>';
            } else if($user->status == 'terminated'){
                return '<label class="label label-danger">Terminated</label>';
            }
            return ucfirst($user->status);
        })
        ->addColumn('clue_progress', function($user){
            $completedClue = $user->hunt_user_details()->where('status','completed')->count();
            $totalClue = $user->hunt_user_details()->count();
            
            return $completedClue.'/'.$totalClue;
        })
        ->addColumn('view', function($user){
            return '<a href="'.route('admin.userHuntDetails',$user->id).'" >More</a>';
        })
        ->addColumn('relic', function($user){
            if($user->relic){
                return $user->relic->name;
            } else if($user->relic_reference){
                return $user->relic_reference->name;
            } else {
                return '-';
            }
        })
        ->rawColumns(['view','status'])
        ->order(function ($query) {
                    if (request()->has('created_at')) {
                        $query->orderBy('created_at', 'DESC');
                    }
                    
                })
        //->setTotalRecords($count)
        //->skipPaging()
        ->make(true);
    }

    //activity
    public function activity($id){
        $user = User::select('gold_balance')
                    ->with([
                            'hunt_user_v1:user_id,hunt_id,hunt_complexity_id,status,hunt_mode,complexity',
                            'hunt_user_v1.hunt:_id,fees',
                            'plans_purchases.plan:_id,name,price,gold_value,type',
                            'plans_purchases.country:name,code,currency,currency_full_name'
                            ])
                    ->where('_id',$id)
                    ->first(); 

        $data['usedGold'] = 0;
        $data['currentGold'] = 0;
        $data['totalGold'] = 0;
        $data['plan_purchase'] = [];
        $data['goldPurchased'] = 0;
        if ($user) {
            $data['currentGold'] = $user->gold_balance;
            $data['totalGold'] = $data['usedGold'] + $data['currentGold'] + $data['goldPurchased'];
            
            if ($user->plans_purchases) {
                $data['goldPurchased'] = $user->plans_purchases->pluck('gold_value')->sum();
                $data['plan_purchase'] = $user->plans_purchases;
            }

            if ($user->hunt_user_v1) {
                $data['usedGold'] = $user->hunt_user_v1->where('hunt_mode','challenge')->pluck('hunt.fees')->sum() + $data['goldPurchased'];
            }

        }
        
        return view('admin.user.activity',compact('id','data'));
    }

    //ADD GOLD
    public  function addGold(Request $request){
        $validator = Validator::make($request->all(), [
            'gold' => 'required|integer',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }        

        $id = $request->get('id');
        $gold = $request->get('gold');
        
        $user = User::where('_id',$id)->first();

        $user->gold_balance = (int)$gold + $user->gold_balance;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' =>'Gold has been added successfully.',
            'current_gold'=> $user->gold_balance
        ]);
    }

    //ADD Skeleton
    public function addSkeletonKey(Request $request){
        
        $validator = Validator::make($request->all(), [
            'skeleton_key' => 'required|integer',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }
        $addSkeletonNumber = $request->get('skeleton_key');

        $user = User::where('_id',$request->get('user_id'))->first();
        
        for ($i=0; $i < $addSkeletonNumber ; $i++) { 
            $skeletonKey = [
                'key'       => new MongoDBId(),
                'created_at'=> new MongoDBDate(),
                'used_at'   => null
            ];
            
            $user->push('skeleton_keys', $skeletonKey);
        }

        $userSk = User::where('_id',$request->get('user_id'))->first();
        if($userSk->skeleton_keys){
            $userSkKeys = collect($userSk->skeleton_keys);
            $availSkKeys = $userSkKeys->where('used_at', null)->count();
        }
        return response()->json([
                                'status'=>true,
                                'message'=> 'Skeleton keys has been added successfully',
                                'available_skeleton_keys'=> $availSkKeys
                            ]);
        
    }


    //PART MANAGE
    public function practiceGameUser($id){
        $practiceGames = PracticeGameUser::where('user_id',$id)
                                        ->with('game:_id,name')
                                        ->orderBy('completion_times','desc')
                                        ->get()
                                        ->map(function($query){
                                            if (isset($query->favourite)) {
                                                if($query->favourite == true){
                                                    $query->favourite = 'true';
                                                } elseif ($query->favourite == false) {
                                                    $query->favourite = 'false';
                                                }
                                            } else {
                                                $query->favourite = '';
                                            }
                                            return $query;
                                        });        
        return view('admin.user.partManage',compact('id','practiceGames'));
    }

    public function eventsUser($id){
        $eventsUser = EventsUser::where('user_id',$id)
                                ->with('event.city:_id,name')
                                ->with('event:_id,city_id,name,starts_at,ends_at')
                                ->get();
                                
        return view('admin.user.events',compact('id','eventsUser'));
    }

    /* planPurchase */
    public function planPurchase($id){
        return view('admin.user.planPurchase',compact('id'));
    }

    public function getPlanPurchaseList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $userId = $request->user_id;
        /*print_r($request->status);
        exit();*/
        $plans = PlanPurchase::select('user_id', 'plan_id', 'country_code', 'gold_value', 'skeleton_keys_amount', 'expandable_skeleton_keys', 'price', 'transaction_id','created_at')
                                ->where('user_id',$userId)
                                ->where(function($query) use ($request){
                                    if ($request->status == "gold") {
                                        $query->whereNotNull('gold_value');
                                    } elseif ($request->status == "skeleton"){
                                        $query->where(function($query1){
                                            $query1->orWhere(function($query2){
                                                $query2->whereNotNull('skeletons_bucket');
                                            })->orWhere(function($query2){
                                                $query2->whereNotNull('skeleton_keys_amount');
                                            })->orWhere(function($query2){
                                                $query2->whereNotNull('skeleton_keys');
                                            });
                                        });
                                    }
                                });

        $admin = Auth::user();
        if($search != ''){
            $plans->where(function($query) use ($search){
                    $query->where('user_id','like','%'.$search.'%')
                    ->orWhere('country_code','like','%'.$search.'%')
                    ->orWhere('transaction_id','like','%'.$search.'%')
                    ->orWhere('gold_value','like','%'.$search.'%');
                })
                /*->with(['plan'=>function($query) use ($search){
                    $query->where(function($query) use ($search){
                        $query->orWhere('name','like','%'.$search.'%');
                    });
                }])
                ->with(['user'=>function($query) use ($search){
                    $query->where(function($query) use ($search){
                        $query->orWhere('first_name','like','%'.$search.'%')
                        ->orWhere('last_name','like','%'.$search.'%');
                    });
                }])*/;
        }
        $plans = $plans->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        
        $count = PlanPurchase::where('user_id',$userId)
                                ->where(function($query) use ($request){
                                    if ($request->status == "gold") {
                                        $query->whereNotNull('gold_value');
                                    } elseif ($request->status == "skeleton"){
                                        $query->where(function($query1){
                                            $query1->orWhere(function($query2){
                                                $query2->whereNotNull('skeletons_bucket');
                                            })->orWhere(function($query2){
                                                $query2->whereNotNull('skeleton_keys_amount');
                                            })->orWhere(function($query2){
                                                $query2->whereNotNull('skeleton_keys');
                                            });
                                        });
                                    }
                                })
                                ->count();
        if($search != ''){
            $count = PlanPurchase::where(function($query) use ($search){
                $query->where('user_id','like','%'.$search.'%')
                ->orWhere('country_code','like','%'.$search.'%')
                ->orWhere('transaction_id','like','%'.$search.'%')
                ->orWhere('gold_value','like','%'.$search.'%');
            })
            ->where('user_id',$userId)
            ->where(function($query) use ($request){
                if ($request->status == "gold") {
                    $query->whereNotNull('gold_value');
                } elseif ($request->status == "skeleton"){
                    $query->where(function($query1){
                        $query1->orWhere(function($query2){
                            $query2->whereNotNull('skeletons_bucket');
                        })->orWhere(function($query2){
                            $query2->whereNotNull('skeleton_keys_amount');
                        })->orWhere(function($query2){
                            $query2->whereNotNull('skeleton_keys');
                        });
                    });
                }
            })
            ->count();
        }
        return DataTables::of($plans)
        ->addIndexColumn()
        ->addColumn('created_at', function($plans){
            return $plans->created_at->format('d-M-Y @ h:i A');
        })
        ->addColumn('name', function($plans){
            return $plans->user->first_name.' '.$plans->user->last_name;
        })
        ->addColumn('total_amount', function($plans){
            return (($plans->plan)?number_format($plans->plan->price,2).' '.$plans->country->currency:'-');
        })
        ->editColumn('gold_value',function($plans){
            return ($plans->gold_value)?$plans->gold_value:'-';
        })
        ->addColumn('purchased_plan', function($plans){
            return (($plans->plan)?$plans->plan->name:'-');
        })
        ->addColumn('payment', function($plans){
            return '-';
        })

        ->addColumn('action', function($plans) use ($admin){
            if($admin->hasPermissionTo('View Users')){
                return '<a href="'.route('admin.accountInfo',$plans->user_id).'" data-toggle="tooltip" title="View" >View</a>';
            }
            return '';
        })
        
        ->rawColumns(['action'])
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

    public function miniGameStatistics($id){
        $user = User::where('_id',$id)->first();
        $games = (new UserHelper)->setUser($user)->getMinigamesStatistics();
        return view('admin.user.mini_game',compact('id','games'));
    }

    public function reserTheUser(Request $request, $id)
    {
        $user = User::find($id);
        $default = new User;
        foreach ($default->getAttributes() as $field => $value) {
            if (
                $field == 'nodes_status' ||
                $field == 'registration_completed' ||
                $field == 'skeleton_keys' ||
                $field == 'gold_balance' ||
                $field == 'skeletons_bucket' ||
                $field == 'pieces_collected' ||
                $field == 'widgets' ||
                $field == 'first_login' ||
                $field == 'tutorials' ||
                $field == 'agent_status' ||
                $field == 'relics' ||
                $field == 'power_status' ||
                $field == 'ar_mode' ||
                $field == 'avatar' ||
                $field == 'nodes_status' ||
                $field == 'buckets'
            ) {
                $user->$field = $value;
            }
        }

        HuntUser::where('user_id', $id)->get()->map(function($huntUser){
            $huntUser->hunt_user_details()->delete();
            $huntUser->delete();
        });
        
        $user->practice_games()->delete();
        $user->plans_purchases()->delete();
        $user->events()->delete();
        $user->user_relic_map_pieces()->orderBy('created_at', 'asc')->skip(1)->get()->each(function($row){ 
            $row->delete(); 
        });

        /** MGC & Minigame Tutorial Games */
        $games = Game::where('status',true)->get();
        $MGCStatus = []; 
        $minigameTutorial = []; 
        foreach ($games as $key => $game) {
            $MGCStatus[] = [ 'game_id' => $game->id, 'completed_at' => null ];
            $minigameTutorial[] = [ 'game_id' => $game->id, 'completed_at' => null ];
        }
        $user->mgc_status = $MGCStatus;
        $user->minigame_tutorials = $minigameTutorial;
        $user->save();
        return response()->json(['message'=> 'Account has been successfully reset.']);
    }
    
    public function tutorialsProgress($id){
        $user = User::where('_id',$id)->select('_id', 'tutorials')->first();

        $tutorials = collect();
        $user->tutorials->map(function($completed, $module) use (&$tutorials){

            if ($module == 'hunt_mg_challenge') {
                $module = 'Hunt MGC';
            }
            $tutorials[$module] = $completed;
            return $completed;
        });
        return view('admin.user.tutorialsProgress',compact('id','tutorials'));        
    }

    public function chestInverntory($id){
        $user = User::where('_id',$id)->select('_id', 'buckets')->first();
        $chests = $user->buckets['chests'];
        $chests['mini_game'] = ($chests['minigame_id'])?Game::where('_id',$chests['minigame_id'])->first()->name:'-';
        return view('admin.user.chestInverntory',compact('id','chests'));           
    }

    public function updateCity(Request $request){
        $validator = Validator::make($request->all(), [
            'dob'   => 'required',
            'city'  => 'required',
        ]);

        if ($validator->fails()){
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }        
        
        $user = User::where('_id',$request->user_id)->first();

        $user->city_id = $request->city;
        $user->dob = Carbon::parse($request->dob);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' =>'City and date of birth has been updated successfully.',
            'current_gold'=> $user->gold_balance
        ]);
    }
}
