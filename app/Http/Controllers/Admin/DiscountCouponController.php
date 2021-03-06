<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v2\DiscountCoupon;
use App\Models\v1\WidgetItem;
use Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use Auth;

class DiscountCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $widgetItem = WidgetItem::get();
        
        return view('admin.discount.coupons',compact('widgetItem'));
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
        if (isset($request['expiry_date_checked'])) {
            $request['expiry_date_checked'] = 'true';
        } else {
            $request['expiry_date_checked'] = 'false';
        }

        if (isset($request['number_of_uses_checked'])) {
            $request['number_of_uses_checked'] = 'true';
        } else {
            $request['number_of_uses_checked'] = 'false';
        }

        $request['discount_code'] = strtoupper($request['discount_code']);
        
        $validator = Validator::make($request->all(),[
            // 'discount_code'    => 'required|exists:discount_coupons,discount_code',
            'discount_code'    => 'required|unique:discount_coupons,discount_code',            
            'discount_types'   => 'required|in:gold_credit,discount_percentage,avatar_item',
            'discount'         => 'numeric',
            'discount'         => 'required_if:discount_types,gold_credit,discount_percentage',
            'number_of_uses'   => 'required_if:number_of_uses_checked,false',
            'can_mutitime_use' => 'required',
            'expiry_date'      => 'required_if:expiry_date_checked,false',
            // 'description'      => 'required',
            'avatar_ids'       => 'required_if:discount_percentage,avatar_item',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }


        $data = $request->all();

        if ($data['expiry_date_checked'] == 'false') {
            $expiryDate = $request->get('expiry_date');
            $dates = explode(' - ', $expiryDate);
            $startDate = Carbon::parse($dates[0]);
            $endDate = Carbon::parse($dates[1]);
        } else {
            $startDate = null;
            $endDate = null;
        }
        
        $data['start_at'] = $startDate;
        $data['end_at']  = $endDate;
        if ($data['discount_types'] == 'gold_credit' || $data['discount_types'] == 'discount_percentage') {
            $data['discount'] = (float)$data['discount'];
        }
        $data['number_of_uses'] = (($data['number_of_uses_checked'] == 'false')?(int)$data['number_of_uses']:null);
        $data['can_mutitime_use'] = ($data['can_mutitime_use']=='true')?true:false;

        DiscountCoupon::create($data);
        return response()->json([
            'status'  => true,
            'message' => 'Discount Coupons has been added successfully.',
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
        $discount = DiscountCoupon::find($id);
        $widgetItem = WidgetItem::get();

        return view('admin.discount.edit_coupons',compact('discount','widgetItem'));
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
        if (isset($request['expiry_date_checked'])) {
            $request['expiry_date_checked'] = 'true';
        } else {
            $request['expiry_date_checked'] = 'false';
        }

        if (isset($request['number_of_uses_checked'])) {
            $request['number_of_uses_checked'] = 'true';
        } else {
            $request['number_of_uses_checked'] = 'false';
        }

        $request['discount_code'] = strtoupper($request['discount_code']);

        $validator = Validator::make($request->all(),[
            'discount_code'    => 'required|unique:discount_coupons,discount_code,'.$id.',_id',
            'discount_types'   => 'required|in:gold_credit,discount_percentage,avatar_item',
            'discount'         => 'numeric',
            'discount'         => 'required_if:discount_types,gold_credit,discount_percentage',
            'number_of_uses'   => 'required_if:number_of_uses_checked,false',
            'can_mutitime_use' => 'required',
            'expiry_date'      => 'required_if:expiry_date_checked,false',
            // 'description'      => 'required',
            'avatar_ids'       => 'required_if:discount_percentage,avatar_item',
        ]);
        
        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $discount = DiscountCoupon::where('_id',$id)->first();
        
        $data = $request->all();
        
        if ($data['expiry_date_checked'] == 'false') {
            $expiryDate = $request->get('expiry_date');
            $dates = explode(' - ', $expiryDate);
            $startDate = Carbon::parse($dates[0]);
            $endDate = Carbon::parse($dates[1]);
        } else {
            $startDate = null;
            $endDate = null;
        }
                
        $data['start_at'] = $startDate;
        $data['end_at']  = $endDate;
        if ($data['discount_types'] == 'gold_credit' || $data['discount_types'] == 'discount_percentage') {
            $data['discount'] = (float)$data['discount'];
            $data['avatar_ids'] = [];
        }
        // $data['discount'] = (float)$data['discount'];
        $data['number_of_uses'] = (($data['number_of_uses_checked'] == 'false')?(int)$data['number_of_uses']:null);
        $data['can_mutitime_use'] = ($data['can_mutitime_use']=='true')?true:false;

        $discount->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Discount Coupons has been updated successfully.',
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
        DiscountCoupon::find($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Discount Coupons has been deleted successfully.',
        ]);
    }

    public function getDiscountsList(Request $request){
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];
        $discount = DiscountCoupon::select('discount_code','discount_types','discount','number_of_uses','start_at','end_at','description','can_mutitime_use','created_at');
        $admin = Auth::user();
        if($search != ''){
            $discount->where(function($query) use ($search){
                    $query->where('user_id','like','%'.$search.'%')
                    ->orWhere('country_code','like','%'.$search.'%')
                    ->orWhere('transaction_id','like','%'.$search.'%')
                    ->orWhere('gold_value','like','%'.$search.'%');
                });
        }
        $discount = $discount->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        
        $count = DiscountCoupon::count();
        if($search != ''){
            $count = DiscountCoupon::where(function($query) use ($search){
                $query->where('user_id','like','%'.$search.'%')
                ->orWhere('country_code','like','%'.$search.'%')
                ->orWhere('transaction_id','like','%'.$search.'%')
                ->orWhere('gold_value','like','%'.$search.'%');
            })->count();
        }
        return DataTables::of($discount)
        ->addIndexColumn()
        ->editColumn('created_at', function($discount){
            return $discount->created_at->format('d-M-Y @ h:i A');
        })
        ->editColumn('discount_types', function($discount){
            return ucfirst(str_replace('_', ' ',$discount->discount_types));
        })
        ->editColumn('discount', function($discount){
            if ($discount->discount_types == 'gold_credit') {
                return $discount->discount.' Gold';
            } else if ($discount->discount_types == 'discount_percentage') {
                return $discount->discount.'%';
            }
        })
        ->editColumn('can_mutitime_use', function($discount){
            return ($discount->can_mutitime_use)?'Yes':'No';
        })
        ->editColumn('start_at', function($discount){
            if (is_null($discount->start_at)) {
                return '-';
            } else {
                return $discount->start_at->format('d M,Y');
            }
        })
        ->editColumn('end_at', function($discount){
            if (is_null($discount->end_at)) {
                return '-';
            } else {
                return $discount->end_at->format('d M,Y');
            }
        })
        ->addColumn('action', function($discount) use ($admin){
            $data = '';
            if($admin->hasPermissionTo('Edit Discount Coupons')){
                $data .=  '<a href="javascript:void(0)" class="edit_discount" data-action="edit" data-id="'.$discount->id.'" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil iconsetaddbox"></i></a>';
            }
            if($admin->hasPermissionTo('Delete Discount Coupons')){
                $data .=  '<a href="javascript:void(0)" class="delete_discount" data-action="delete" data-placement="left" data-id="'.$discount->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>
                </a>';
            }
            return $data;
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
