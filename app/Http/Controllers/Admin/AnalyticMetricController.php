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

class AnalyticMetricController extends Controller
{
    public function analyticsMetrics(Request $request){
        $user = User::get();
        $startAt = $user->first()->created_at;
        $endAt = $user->last()->created_at;
        $data['user_start_date'] = $startAt;
        $data['user_end_date'] = $endAt;
        
        // $user = User::whereBetween('created_at', [$startAt,$endAt])->get();
        
        $currentDate = Carbon::now();

        $planPurchase = PlanPurchase::whereHas('user')
                                        ->get();

        $data['plan_purchase_start_date'] = $planPurchase->first()->created_at;
        $data['plan_purchase_end_date'] = $planPurchase->last()->created_at;

        $widgetItem = WidgetItem::get();
        $huntUser = HuntUser::whereHas('user')
                                ->get();
        
        $data['hunt_user_start_date'] = $huntUser->first()->created_at;
        $data['hunt_user_end_date'] = $huntUser->last()->created_at;

        $eventUser = EventsUser::with('event')
                                ->whereHas('user')
                                ->whereHas('event')
                                ->get();        


        $data['event_user_start_date'] = $eventUser->first()->created_at;
        $data['event_user_end_date'] = $eventUser->first()->created_at;

        /* STORE */
        $data['total_user'] = $user->count();
        $data['total_purchase'] =  $planPurchase->count();
        $data['total_amount_purchase'] =  $planPurchase->sum('price');
        $totalPurchase = $data['total_amount_purchase'];
        if ($data['total_purchase'] == 0) {
            $totalPurchase = 1;
        }
        $data['average_amount_purchase'] =  $data['total_amount_purchase']/$totalPurchase;
        $data['total_coins_purchase'] =  $planPurchase->sum('gold_value');
        $data['average_revenue'] =  round($planPurchase->count('user_id')/$data['total_user']);

        $totalPlanPurchase = $planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count();
        if ($totalPlanPurchase == 0) {
            $totalPlanPurchase = 1;
        }
        $data['average_skeleton_keys_purchased'] =  round($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count()/$totalPlanPurchase);
        $data['total_amount_skeleton_keys_purchased'] =  '$'.number_format($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->sum('price'),2);
        $data['total_revenue_google_fees'] =  '$'.number_format((30/100)*$planPurchase->sum('price'),2);
        $data['total_revenue_apple_fees'] = '-';
        
        /* END STORE */


        /* Avatar */
        foreach ($user->pluck('widgets.*.id') as $key => $value) {
            foreach ($value as $key => $widgets) {
                $widgetsId[] = $widgets;
            }
        }

        $totalWidgetsId = array_count_values($widgetsId);
        arsort($totalWidgetsId);
        //$data['total_items_purchased'] = $totalWidgetsId;
        $data['total_items_purchased'] = [];
        /*foreach ($totalWidgetsId as $key => $value) {
            if (file_exists(public_path('admin_assets/widgets/'.$key.'.png'))){
                $data['total_items_purchased'][] = [
                                                        'image' => asset('admin_assets/widgets/'.$key.'.png'),
                                                        'total_use'=>$value,
                                                    ];
            }
        }*/
        $data['average_avatar_items_purchased'] = round(count($widgetsId)/$widgetItem->count());

        $data['total_paid_avatar'] = 0;
        foreach ($totalWidgetsId as $key => $value) {
            $widgetItem = WidgetItem::select('item_name','widget_name','gold_price')
                                    ->where('_id',$key)->first();
            $data['total_paid_avatar']+= $widgetItem->gold_price*$value;
        }

        /* END Avatar */

        /* USER */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_avtar_user'] = $data['total_male']+$data['total_female'];
        $data['per_male'] = number_format(($data['total_male']/$data['total_avtar_user'])*100,2).'%';
        $data['per_female'] = number_format(($data['total_female']/$data['total_avtar_user'])*100,2).'%';
        /* END USER */

        /* HUNT CLUE */
        $data['total_hunt_complated'] = $huntUser->count();
        if ($data['total_hunt_complated'] == 0) {
            $data['total_hunt_complated'] = 1;
        }
        $data['per_completed_hunt'] = round(($huntUser->where('status','completed')->count()/$data['total_hunt_complated'])*100).'%';
        $data['per_completed_hunt_user'] = round(($huntUser->where('status','completed')->count()/$user->count())*100).'%';
        $data['average_hunts_completed'] = round($huntUser->whereIn('status','completed')->count()/$data['total_hunt_complated']);
        
        $data['user_clue_1'] = $huntUser->where('complexity',1)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_2'] = $huntUser->where('complexity',2)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_3'] = $huntUser->where('complexity',3)
                                        ->where('status','running')
                                        ->count();
        
        $data['user_clue_today_1'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',1)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_2'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',2)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();


        
        $data['total_hunt_complated'] = $huntUser->where('status','completed')->count();
        $huntCompltedClue = [];

        foreach ($huntUser->where('status','completed') as $key => $value) {
            $start_date = Carbon::parse($value->started_at);
            $end_date = Carbon::parse($value->ended_at);
            $huntCompltedClue[] = $start_date->diffInDays($end_date);
        }
        // /echo "<pre>";
        asort($huntCompltedClue);
        $data['hunt_complted_clue'] = array_count_values($huntCompltedClue);

        /* END HUNT CLUE */

        /* EVENT */
       
        $data['user_event_city'] = count(array_unique($eventUser->pluck('event.city.name')->toArray())); 
        $data['user_event_country'] = count(array_unique($eventUser->pluck('event.city.country.name')->toArray()));

        $data['amount_revenue_event_paid_coins'] = '$'.number_format($eventUser->pluck('event.fees')->sum(),2);

        /* END EVENT */

        // return $data;
        return view('admin.analytics_metrics',compact('data'));   
    }

    public function analyticsMetricsFilter(Request $request){
        $date = explode('-', $request->get('date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');

        $user = User::whereBetween('created_at', [$startAt,$endAt])
                        ->get();
        $data['start_date'] = $startAt;
        $data['end_date'] = $endAt;
        
        $currentDate = Carbon::now();

        $planPurchase = PlanPurchase::whereHas('user',function($query) use ($startAt,$endAt){
                                            $query->whereBetween('created_at', [$startAt,$endAt]);
                                        })
                                        ->get();

        $widgetItem = WidgetItem::get();
        $huntUser = HuntUser::whereHas('user',function($query) use ($startAt,$endAt){
                                    $query->whereBetween('created_at', [$startAt,$endAt]);
                                })
                                ->get();
        $eventUser = EventsUser::with('event')
                                ->whereHas('user',function($query) use ($startAt,$endAt){
                                    $query->whereBetween('created_at', [$startAt,$endAt]);
                                })
                                ->whereHas('event')
                                ->get();        


        /* STORE */
        $data['total_user'] = $user->count();
        
        $data['total_purchase'] =  $planPurchase->count();
        $data['total_amount_purchase'] =  $planPurchase->sum('price');
        $totalPurchase = $data['total_amount_purchase'];
        if ($data['total_purchase'] == 0) {
            $totalPurchase = 1;
        }
        $data['average_amount_purchase'] =  $data['total_amount_purchase']/$totalPurchase;
        $data['total_coins_purchase'] =  $planPurchase->sum('gold_value');
        $data['average_revenue'] =  round($planPurchase->count('user_id')/$data['total_user']);

        $totalPlanPurchase = $planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count();
        if ($totalPlanPurchase == 0) {
            $totalPlanPurchase = 1;
        }
        $data['average_skeleton_keys_purchased'] =  round($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count()/$totalPlanPurchase);
        $data['total_amount_skeleton_keys_purchased'] =  '$'.number_format($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->sum('price'),2);
        $data['total_revenue_google_fees'] =  '$'.number_format((30/100)*$planPurchase->sum('price'),2);
        $data['total_revenue_apple_fees'] = '-';
        
        /* END STORE */


        /* Avatar */
        foreach ($user->pluck('widgets.*.id') as $key => $value) {
            foreach ($value as $key => $widgets) {
                $widgetsId[] = $widgets;
            }
        }

        $totalWidgetsId = array_count_values($widgetsId);
        arsort($totalWidgetsId);
        //$data['total_items_purchased'] = $totalWidgetsId;
        $data['total_items_purchased'] = [];
        foreach ($totalWidgetsId as $key => $value) {
            if (file_exists(public_path('admin_assets/widgets/'.$key.'.png'))){
                $data['total_items_purchased'][] = [
                                                        'image' => asset('admin_assets/widgets/'.$key.'.png'),
                                                        'total_use'=>$value,
                                                    ];
            }
        }
        $data['average_avatar_items_purchased'] = round(count($widgetsId)/$widgetItem->count());

        $data['total_paid_avatar'] = 0;
        foreach ($totalWidgetsId as $key => $value) {
            $widgetItem = WidgetItem::select('item_name','widget_name','gold_price')
                                    ->where('_id',$key)->first();
            $data['total_paid_avatar']+= $widgetItem->gold_price*$value;
        }
        /* END Avatar */

        /* USER */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_avtar_user'] = $data['total_male']+$data['total_female'];
        $data['per_male'] = number_format(($data['total_male']/$data['total_avtar_user'])*100,2).'%';
        $data['per_female'] = number_format(($data['total_female']/$data['total_avtar_user'])*100,2).'%';
        /* END USER */

        /* HUNT CLUE */
        $data['total_hunt_complated'] = $huntUser->count();
        if ($data['total_hunt_complated'] == 0) {
            $data['total_hunt_complated'] = 1;
        }
        $data['per_completed_hunt'] = round(($huntUser->where('status','completed')->count()/$data['total_hunt_complated'])*100).'%';
        $data['per_completed_hunt_user'] = round(($huntUser->where('status','completed')->count()/$user->count())*100).'%';
        $data['average_hunts_completed'] = round($huntUser->whereIn('status','completed')->count()/$data['total_hunt_complated']);
        
        $data['user_clue_1'] = $huntUser->where('complexity',1)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_2'] = $huntUser->where('complexity',2)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_3'] = $huntUser->where('complexity',3)
                                        ->where('status','running')
                                        ->count();
        
        $data['user_clue_today_1'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',1)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_2'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',2)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        
        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        
        $data['total_hunt_complated'] = $huntUser->where('status','completed')->count();
        $huntCompltedClue = [];

        foreach ($huntUser->where('status','completed') as $key => $value) {
            $start_date = Carbon::parse($value->started_at);
            $end_date = Carbon::parse($value->ended_at);
            $huntCompltedClue[] = $start_date->diffInDays($end_date);
        }
        // /echo "<pre>";
        asort($huntCompltedClue);
        $data['hunt_complted_clue'] = array_count_values($huntCompltedClue);
        /* END HUNT CLUE */

        /* EVENT */
       
        $data['user_event_city'] = count(array_unique($eventUser->pluck('event.city.name')->toArray())); 
        $data['user_event_country'] = count(array_unique($eventUser->pluck('event.city.country.name')->toArray())); 

        $data['amount_revenue_event_paid_coins'] = '$'.number_format($eventUser->pluck('event.fees')->sum(),2);
        /* END EVENT */

        // return $data;
        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }

    public function getStoreDateFilter(Request $request){
        $user = User::get();


        $date = explode('-', $request->get('store_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');


        $planPurchase = PlanPurchase::whereHas('user')
                                    ->whereBetween('created_at', [$startAt,$endAt])
                                    ->get();

        /* STORE */
        $data['total_user'] = $user->count();
        $data['total_purchase'] =  $planPurchase->count();
        $data['total_amount_purchase'] =  $planPurchase->sum('price');
        $totalPurchase = $data['total_amount_purchase'];
        if ($data['total_purchase'] == 0) {
            $totalPurchase = 1;
        }
        $data['average_amount_purchase'] =  $data['total_amount_purchase']/$totalPurchase;
        $data['total_coins_purchase'] =  $planPurchase->sum('gold_value');
        $data['average_revenue'] =  round($planPurchase->count('user_id')/$data['total_user']);

        $totalPlanPurchase = $planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count();
        if ($totalPlanPurchase == 0) {
            $totalPlanPurchase = 1;
        }
        $data['average_skeleton_keys_purchased'] =  round($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count()/$totalPlanPurchase);
        $data['total_amount_skeleton_keys_purchased'] =  '$'.number_format($planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->sum('price'),2);
        $data['total_revenue_google_fees'] =  '$'.number_format((30/100)*$planPurchase->sum('price'),2);
        $data['total_revenue_apple_fees'] = '-';
        
        /* END STORE */

         return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }

    public function getUserDateFilter(Request $request){

        $date = explode('-', $request->get('user_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');

        $user = User::whereBetween('created_at', [$startAt,$endAt])
                        ->get();

        $widgetItem = WidgetItem::get();

        /* USER */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_avtar_user'] = $data['total_male']+$data['total_female'];
        $data['per_male'] = number_format(($data['total_male']/$data['total_avtar_user'])*100,2).'%';
        $data['per_female'] = number_format(($data['total_female']/$data['total_avtar_user'])*100,2).'%';
        /* END USER */

        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);

    }

    public function getAvtarDateFilter(Request $request){
        $date = explode('-', $request->get('avtar_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');

        $user = User::whereBetween('created_at', [$startAt,$endAt])
                        ->get();

        $widgetItem = WidgetItem::get();

        /* Avatar */
        foreach ($user->pluck('widgets.*.id') as $key => $value) {
            foreach ($value as $key => $widgets) {
                $widgetsId[] = $widgets;
            }
        }

        $totalWidgetsId = array_count_values($widgetsId);
        arsort($totalWidgetsId);
        //$data['total_items_purchased'] = $totalWidgetsId;
        $data['total_items_purchased'] = [];
        foreach ($totalWidgetsId as $key => $value) {
            if (file_exists(public_path('admin_assets/widgets/'.$key.'.png'))){
                $data['total_items_purchased'][] = [
                                                        'image' => asset('admin_assets/widgets/'.$key.'.png'),
                                                        'total_use'=>$value,
                                                    ];
            }
        }
        $data['average_avatar_items_purchased'] = round(count($widgetsId)/$widgetItem->count());

        $data['total_paid_avatar'] = 0;
        foreach ($totalWidgetsId as $key => $value) {
            $widgetItem = WidgetItem::select('item_name','widget_name','gold_price')
                                    ->where('_id',$key)->first();
            $data['total_paid_avatar']+= $widgetItem->gold_price*$value;
        }
        /* END Avatar */

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

        /* HUNT CLUE */
        $data['total_hunt_complated'] = $huntUser->count();
        if ($data['total_hunt_complated'] == 0) {
            $data['total_hunt_complated'] = 1;
        }
        $data['per_completed_hunt'] = round(($huntUser->where('status','completed')->count()/$data['total_hunt_complated'])*100).'%';
        $data['per_completed_hunt_user'] = round(($huntUser->where('status','completed')->count()/$user->count())*100).'%';
        $data['average_hunts_completed'] = round($huntUser->whereIn('status','completed')->count()/$data['total_hunt_complated']);
        
        $data['user_clue_1'] = $huntUser->where('complexity',1)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_2'] = $huntUser->where('complexity',2)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_3'] = $huntUser->where('complexity',3)
                                        ->where('status','running')
                                        ->count();
        
        $data['user_clue_today_1'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',1)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_2'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',2)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        
        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        
        $data['total_hunt_complated'] = $huntUser->where('status','completed')->count();
        $huntCompltedClue = [];

        foreach ($huntUser->where('status','completed') as $key => $value) {
            $start_date = Carbon::parse($value->started_at);
            $end_date = Carbon::parse($value->ended_at);
            $huntCompltedClue[] = $start_date->diffInDays($end_date);
        }
        // /echo "<pre>";
        asort($huntCompltedClue);
        $data['hunt_complted_clue'] = array_count_values($huntCompltedClue);
        /* END HUNT CLUE */

        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }


    public function getEventDateFilter(Request $request){
        $date = explode('-', $request->get('event_date'));
        $startAt = new \DateTime(date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[0])))));
        $endAt= new \DateTime((date('Y-m-d',strtotime(str_replace(' ', '-', trim($date[1]))))));
        $endAt->modify('+1 day');

        $eventUser = EventsUser::with('event')
                                ->whereHas('user')
                                ->whereHas('event')
                                ->whereBetween('created_at', [$startAt,$endAt])
                                ->get();

        /* EVENT */
        $data['user_event_city'] = count(array_unique($eventUser->pluck('event.city.name')->toArray())); 
        $data['user_event_country'] = count(array_unique($eventUser->pluck('event.city.country.name')->toArray()));

        $data['amount_revenue_event_paid_coins'] = '$'.number_format($eventUser->pluck('event.fees')->sum(),2);
        /* END EVENT */

        return response()->json([
            'status'  => true,
            'message' => 'get data successfully',
            'data'    => $data,
        ]);
    }

    public function getAnalytic(){
        $startAt = Carbon::now()->subDays(30);
        $endAt = Carbon::now();
        $currentDate = Carbon::now();

        $allUser = User::get();
        $allPlanPurchase = PlanPurchase::get();
        $user =  $allUser->whereBetween('created_at', [$startAt,$endAt]);
        $widgetItem = WidgetItem::get();
        $allHuntUser = HuntUser::get();
        $huntUser = $allHuntUser->whereBetween('created_at', [$startAt,$endAt]);
        

        $planPurchase = $allPlanPurchase->whereBetween('created_at', [$startAt,$endAt]);

        /* STORE */
        $data['total_user'] = $allUser->count();
        $data['total_purchase'] =  $planPurchase->count();
        $data['total_amount_purchase'] =  $planPurchase->sum('price');
        $data['average_amount_purchase'] =  $data['total_amount_purchase']/$data['total_purchase'];
        $data['total_coins_purchase'] =  $planPurchase->sum('gold_value');
        $data['average_revenue'] =  round($planPurchase->count('user_id')/$data['total_user']);
        $data['average_skeleton_keys_purchased'] =  $allPlanPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count()/$planPurchase->where('plan_id','com.ironbridge1779.roguesatlas.gold8')->count();
        $data['total_revenue']['google'] =  number_format((30/100)*$planPurchase->sum('price'),2);
        $data['total_revenue']['apple'] = number_format((30/100)*$planPurchase->sum('price'),2);
        
        /* END STORE */


        /* Avatar */
        foreach ($user->pluck('widgets.*.id') as $key => $value) {
            foreach ($value as $key => $widgets) {
                $widgetsId[] = $widgets;
            }
        }

        $totalWidgetsId = array_count_values($widgetsId);
        arsort($totalWidgetsId);
        // $data['total_items_purchased'] = $totalWidgetsId;
        $data['total_items_purchased'] = [];
        foreach ($totalWidgetsId as $key => $value) {
            if (file_exists(public_path('admin_assets/widgets/'.$key.'.png'))){
                $data['total_items_purchased'][] = [
                                                        'image' => asset('admin_assets/widgets/'.$key.'.png'),
                                                        'total_use'=>$value,
                                                    ];
            }
        }
        $data['average_avatar_items_purchased'] = count($widgetsId)/$widgetItem->count();

        $data['total_paid_avatar'] = 0;
        foreach ($totalWidgetsId as $key => $value) {
            $widgetItem = WidgetItem::select('item_name','widget_name','gold_price')
                                    ->where('_id',$key)->first();
            $data['total_paid_avatar']+= $widgetItem->gold_price*$value;
        }
        /* END Avatar */

        /* USER */
        $data['total_male'] = $user->where('gender','male')->count();
        $data['total_female'] = $user->where('gender','female')->count();
        $data['total_avtar_user'] = $data['total_male']+$data['total_female'];
        $data['per_male'] = ($data['total_male']/$data['total_avtar_user'])*100;
        $data['per_female'] = ($data['total_female']/$data['total_avtar_user'])*100;
        /* END USER */

        /* HUNT */
        $data['per_completed_hunt'] = ($huntUser->where('status','completed')->count()/$huntUser->count())*100;
        $data['total_hunt_complated'] = $huntUser->count();
        $data['average_hunts_completed'] = $huntUser
                                            ->whereIn('status','completed')
                                            ->count()/$data['total_hunt_complated'];
        
        /* END HUNT */

        /* EVENT */
        $eventUser = EventsUser::with('event')
                                ->whereBetween('created_at', [$startAt,$endAt])
                                ->get();
        $data['user_event_city'] = count(array_unique($eventUser->pluck('event.city.name')->toArray())); 
        $data['user_event_country'] = count(array_unique($eventUser->pluck('event.city.country.name')->toArray())); 
        /* END EVENT */


        /* CLUE */
        $data['user_clue_1'] = $huntUser->where('complexity',1)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_2'] = $huntUser->where('complexity',2)
                                        ->where('status','running')
                                        ->count();

        $data['user_clue_3'] = $huntUser->where('complexity',3)
                                        ->where('status','running')
                                        ->count();
        
        $data['user_clue_today_1'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',1)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_2'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',2)
                                        ->whereIn('status',['running','paused'])
                                        ->count();
        

        $data['user_clue_today_3'] = $huntUser
                                        ->where('started_at', '>', $currentDate)
                                        ->where('complexity',3)
                                        ->whereIn('status',['running','paused'])
                                        ->count();

        return $data;
    }
}
