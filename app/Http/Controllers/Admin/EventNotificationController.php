<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v3\City;
use App\Notifications\EventNotification;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;

class EventNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::whereHas('events', function($query){
            $query->where('time.start', '<=', new UTCDateTime(now()->addMonth()))->whereNull('started_at');
        })
        ->get();
        return view('admin.events.notifications', [
            'cities'=> $cities,
            'countries'=> collect()
        ]);
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
            'target'=> 'required|in:BYCOUNTRY,BYCITY',
            'target_audience'=> 'required|in:LOCALS,!LOCALS',
            'cities'=> 'array|required_without:countries',
            'countries'=> 'array|required_without:cities',
            'title'=> 'required',
            'message'=> 'required'
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        /** Send the message to local people leaving in cities mentioned in cities parameter. **/
        $msgToLocalsByCityIds = ($request->target == 'BYCITY' && $request->target_audience == 'LOCALS')? true: false;
        
        /** Send the message to local people leaving in country mentioned in countries parameter. **/
        $msgToLocalsByCountyIds = ($request->target == 'BYCOUNTRY' && $request->target_audience == 'LOCALS')? true: false;
        
        /** Send the message to people which are not leaving in cities mentioned in cities parameter. **/
        $msgToOutsidersByCityIds = ($request->target == 'BYCITY' && $request->target_audience == '!LOCALS')? true: false;
        
        /** Send the message to people which are not leaving in cities mentioned in countries parameter. **/
        $msgToOutsidersByCountyIds = ($request->target == 'BYCOUNTRY' && $request->target_audience == '!LOCALS')? true: false;

        $users = (new UserRepository)->getModel()
                ->when($msgToLocalsByCityIds, function($query) use ($request){
                    $query->whereIn('city_id', $request->cities);
                })
                ->when($msgToLocalsByCountyIds, function($query) use ($request){
                    $query->whereHas('city.country', function($query) use ($request){
                        return $query->whereIn('_id', $request->countries);
                    });
                })
                ->when($msgToOutsidersByCityIds, function($query) use ($request){
                    $query->whereNotIn('city_id', $request->cities);
                })
                ->when($msgToOutsidersByCountyIds, function($query) use ($request){
                    $query->doesntHave('city.country', function($query) use ($request){
                        return $query->whereIn('_id', $request->countries);
                    });
                })
                ->get();

        Notification::send($users, new EventNotification($request->title, $request->message));
        return response()->json(['message'=> 'We have sent notification to '.$users->count().' users']);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
