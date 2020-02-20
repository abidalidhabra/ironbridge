<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\HuntStatistic;
use App\ReportedLocation;
use App\Repositories\AppStatisticRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ReportLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reported_locations.index', [
            'totalSubmitted'=> ReportedLocation::notSended()->count(),
            'totalSubmittedToGoogle'=> ReportedLocation::sent()->count(),
            'huntStatistics'=> HuntStatistic::first(['_id', 'reported_loc_count'])
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
        //
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

    public function list(Request $request)
    {
        $skip = (int)$request->get('start');
        $take = (int)$request->get('length');
        $search = $request->get('search')['value'];

        $reportedLocations = ReportedLocation::when($search != '', function($query) use ($search) {
            $active = ($search == 'true' || $search == 'Active')? true: false;
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('locationName','like','%'.$search.'%')
            ->orWhere('reasons','like','%'.$search.'%')
            ->orWhere('reasonDetails','like','%'.$search.'%')
            ->orWhere('requestId','like','%'.$search.'%')
            ->orWhere('sent_at','like','%'.$search.'%')
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->orderBy('created_at','desc')
        ->skip($skip)
        ->take($take)
        ->get();
        
        /** Filter Result Total Count  **/
        $filterCount = ReportedLocation::when($search != '', function($query) use ($search) {
            $query->where('_id','like','%'.$search.'%')
            ->orWhere('locationName','like','%'.$search.'%')
            ->orWhere('reasons','like','%'.$search.'%')
            ->orWhere('reasonDetails','like','%'.$search.'%')
            ->orWhere('requestId','like','%'.$search.'%')
            ->orWhere('sent_at','like','%'.$search.'%')
            ->orWhere('created_at','like','%'.$search.'%');
        })
        ->count();
                
        $admin = auth()->User();
        return DataTables::of($reportedLocations)
        ->addIndexColumn()
        ->editColumn('locationName', function($relic){
            if (substr_count($relic->locationName, 'generatedPlayableLocations/')) {
                $name = str_replace('generatedPlayableLocations/', '', $relic->locationName);
            }else {
                $name = str_replace('curatedPlayableLocations/', '', $relic->locationName);
            }
            return '<a href="javascript:void(0)">'.$name.'</a>';
        })
        ->editColumn('reasons', function($relic){
            return implode(',', $relic->reasons);
        })
        ->editColumn('reasonDetails', function($relic){
            return '<a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="'.$relic->reasonDetails.'">'.str::limit($relic->reasonDetails, 20).'</a>';
           return ;
        })
        // ->editColumn('requestId', function($relic){
        //     return $relic->requestId ?? '--';
        // })
        ->editColumn('created_at', function($relic){
            return $relic->created_at->format('d-M-Y @ h:i A');
        })
        // ->editColumn('sent_at', function($relic){
        //     return ($relic->sent_at)? '<span class="badge badge-success">'.$relic->sent_at->format('d-M-Y @ h:i A').'</span>':  '<span class="badge badge-warning">Not sent</span>';
        // })
        ->rawColumns(['locationName', 'reasonDetails', 'sent_at'])
        ->setTotalRecords(ReportedLocation::count())
        ->setFilteredRecords($filterCount)
        ->skipPaging()
        ->make(true);
    }

    public function submit(Request $request)
    {
        try {
            
            $locations = ReportedLocation::notSended()->get();

            if (!$locations->count()) {
                return response()->json(['message'=> 'All reported location has been submitted to Google already.']);
            }
            $loc = $locations->map(function($data) {
                $data->languageCode = "en-US";
                unset($data->_id, $data->user_id, $data->sent_at, $data->created_at, $data->updated_at);
                return $data;
            });

            $paybleGoogleKey = (new AppStatisticRepository)->first(['_id', 'google_keys.web'])->google_keys['web'];
            $requestId = uniqid();
            $client = new Client();
            $apiResponse = $client->request('POST', 
                "https://playablelocations.googleapis.com/v3:logPlayerReports?key=".$paybleGoogleKey, 
                [
                    'json' => ["playerReports"=> $loc->toArray(), 'requestId'=> $requestId],
                    'http_errors'=> false
                ]
            );

            $locations->each(function($location) use ($requestId){
                $location->sent_at = now();
                $location->requestId = $requestId;
                $location->save();
            });

            $response = json_decode($apiResponse->getBody()->getContents());
            return response()->json(['message'=> 'Location has been reported successfully.']);
        } catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()]);
        }
    }

    public function updateIt(Request $request)
    {
        $huntStatistics = HuntStatistic::first(['_id', 'reported_loc_count']);
        $huntStatistics->reported_loc_count = (int)$request->reported_loc_count;
        $huntStatistics->save();
        return response()->json(['message'=> 'Location Bunch size has been updated successfully.']);
    }
}
