<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\v1\Admin;
use App\Models\v1\AdminPasswordSetLink;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\EloquentDataTable;
use Maklad\Permission\Models\Permission;
use Validator;
use Carbon\Carbon;
use Auth;
use Notification;
use App\Notifications\AdminPasswordSet;

class AdminManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $permissions = Permission::get();
        $permissions = Permission::get()
                                ->groupBy('module');
        
        return view('admin.adminManagement.adminList',compact('permissions'));
    }

    public function getAdminList(Request $request){

        $user_id = Auth::user()->_id;
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $admin = Admin::role('Admin')->select( 'email','created_at');
        if($search != ''){
            $admin->where(function($query) use ($search){
                $query->where('email','like','%'.$search.'%')
                ->orWhere('created_at','like','%'.$search.'%');
            });
        }
        $admin = $admin->orderBy('created_at','DESC')->skip($skip)->take($take)->get();
        $count = Admin::role('Admin')->count();
        if($search != ''){
            $count = Admin::role('Admin')
            ->where(function($query) use ($search){
              $query->where('email','like','%'.$search.'%')
              ->orWhere('created_at','like','%'.$search.'%');
          })->count();
        }
        return DataTables::of($admin)
        ->addIndexColumn()
        ->editColumn('created_at', function($admin){
            return Carbon::parse($admin->created_at)->format('d-M-Y @ h:i A');
        })
        ->addColumn('resend_mail', function($admin){
            return '<a href="javascript:void(0)" class="resend_mail" id="resend_mail'.$admin->_id.'" data-id="'.$admin->_id.'"><i class="fa fa-repeat iconsetaddbox"></i></a>';
        })
        ->addColumn('action', function($admin){
            return '<a href="javascript:void(0)" class="edit_admin" id="edit_admin'.$admin->_id.'" data-id="'.$admin->_id.'"><i class="fa fa-pencil iconsetaddbox"></i></a> <a href="javascript:void(0)" class="delete_company" data-action="delete" data-placement="left" data-id="'.$admin->id.'"  title="Delete" data-toggle="tooltip"><i class="fa fa-trash iconsetaddbox"></i>';
        })
        ->rawColumns(['action','resend_mail'])
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
        'email'     => 'required|email|unique:admins,email',
        'permissions'  => 'required',
    ]);

     if ($validator->fails())
     {
        $message = $validator->messages()->first();
        return response()->json(['status' => false,'message' => $message]);
    }


    $data = $request->all();
       // print_r( $data['permissions']);die();
    $admin = Admin::create(['email' => $data['email'],'password'=>Hash::make('123456')]);
    $admin->assignRole('Admin');

    $admin->givePermissionTo($data['permissions']);
    $token = str_random(60);
    $admin->notify(new AdminPasswordSet($token));
    //app('auth.password.admins')->createToken($admin);
    $admin->passwordSetLink()->create(['token'=> $token]);

    return response()->json([
        'status' => true,
        'message'=>'User has been added successfully.',
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
       $admin = Admin::where('_id',$id)->first();
       $assignedPermissions = $admin->getAllPermissions()->pluck('_id')->toArray();
       //$permissions = Permission::all();
        $permissions = Permission::get()
                                ->groupBy('module');
       return view('admin.adminManagement.editAdmin',compact('admin','assignedPermissions','permissions'));

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
        $validator = Validator::make($request->all(),[
            'email'     => 'required|email',
            'permissions'  => 'required',
        ]);
        $admin = Admin::where('_id',$id)->first();
        $validator->sometimes('email', 'unique:admins,email', function ($request) use ($admin) {
            return $request->email !== $admin->email;
        });

        if ($validator->fails())
        {
            $message = $validator->messages()->first();
            return response()->json(['status' => false,'message' => $message]);
        }

        $data = $request->all();
        Admin::where('_id',$id)->update(['email' => $data['email']]);

        $admin->syncPermissions($data['permissions']);

        return response()->json([
            'status' => true,
            'message'=>'User has been added successfully.',
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
        $admin = Admin::where('_id',$id)->first();
        $permissions = $admin->getPermissionNames()->toArray();
        foreach ($permissions as $key => $permission) {
            $admin->revokePermissionTo($permission);
        }

        $permissions = $admin->getPermissionNames()->toArray();
        $admin->removeRole('Admin');
        $admin->delete();

        return response()->json([
            'status' => true,
            'message'=>'Admin has been deleted successfully.',
        ]);
    }

    /* RESEND MAIL */
    public function resendMail($id){
        $admin = Admin::where('_id',$id)->first();
        
        $admin->update(['password'=>Hash::make('123456')]);
        $admin->assignRole('Admin');

        // $admin->givePermissionTo($data['permissions']);
        $token = str_random(60);
        $admin->notify(new AdminPasswordSet($token));
        $admin->passwordSetLink()->create(['token'=> $token]);

        return response()->json([
            'status' => true,
            'message'=>'Mail has been sent successfully.',
        ]);
    }
}
