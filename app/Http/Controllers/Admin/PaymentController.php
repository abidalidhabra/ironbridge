<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use App\Models\v2\PlanPurchase;
use Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $plans = PlanPurchase::get();
    	$data['collected'] =  number_format($plans->sum('price'),2);
        $data['android'] =  number_format('0');
        $data['ios'] =  number_format('0');
        return view('admin.payment.index',compact('data'));
    }

    public function getPaymentList(Request $request){
    	$skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $plans = PlanPurchase::select('user_id', 'plan_id', 'country_code', 'gold_value', 'skeleton_keys_amount', 'expandable_skeleton_keys', 'price', 'transaction_id','created_at');
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
        
        $count = PlanPurchase::count();
        if($search != ''){
            $count = PlanPurchase::where(function($query) use ($search){
                $query->where('user_id','like','%'.$search.'%')
                ->orWhere('country_code','like','%'.$search.'%')
                ->orWhere('transaction_id','like','%'.$search.'%')
                ->orWhere('gold_value','like','%'.$search.'%');
            })->count();
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

            return (($plans->plan)?$plans->plan->price.' '.$plans->country->currency:'-');
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
}
