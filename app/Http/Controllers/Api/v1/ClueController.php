<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\Hunt;
use App\Models\v1\HuntComplexitie;
use App\Models\v1\HuntUser;
use App\Models\v1\HuntUserDetail;
use Validator;
use Auth;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Carbon\Carbon;

class ClueController extends Controller
{
    //CLUE complete
    public function clueRevealed(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id",
                        'time'=> "required|integer",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();
        $id = $request->get('hunt_user_details_id');
        $data = [
                    'revealed_at' => new MongoDBDate(),
                    'finished_in' => (int)$request->get('time'),
                    'status'      => 'completed'
                ];
        $huntUserDetail = HuntUserDetail::where('_id',$id)->first();
        $huntUserDetail->update($data);

        if ($huntUserDetail) {
            $clueDetail = HuntUserDetail::where('hunt_user_id',$huntUserDetail->hunt_user_id)
                            ->whereIn('status',['progress','pause'])
                            ->count();

            if ($clueDetail == 0) {
                HuntUser::where([
                                    '_id'=>$huntUserDetail->hunt_user_id,
                                    'user_id'=>$user->id,
                                ])
                        ->update(['status'=>'completed']);
            }

        }
        return response()->json([
                                'status'=>true,
                                'message'=>'Revealed updated successfully '
                            ]);
    }

    //CLUE INFO
    public function clueInfo(Request $request){
        $user = Auth::User();        
        $huntUser = HuntUser::with([
                                    'hunt_user_details:_id,hunt_user_id,revealed_at,finished_in,status',
                                    'hunt:_id,name,place_name'
                                ])
                            ->where('user_id',$user->id)
                            ->first();

        $clue_complete = $huntUser->hunt_user_details->pluck('revealed_at')->filter()->count();
        $data = [
                    'hunt_name' => ($huntUser->hunt->name != "")?$huntUser->hunt->name:$huntUser->hunt->place_name,
                    'clues' => $clue_complete.' Of '.$huntUser->hunt_user_details->count(),
                    'distance' => 0,
                    'current_time' => '00:00:00',
                ];
        return response()->json([
                                'status'  => true,
                                'message' => 'user has been retrieved successfully',
                                'data'    => $data
                            ]);
    }


    //CLUE BASED GAME DETAILS
    public function clueGame(Request $request){
        $validator = Validator::make($request->all(),[
                        'hunt_user_details_id' => "required|exists:hunt_user_details,_id",
                    ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->messages()],422);
        }

        $user = Auth::User();
        $huntUserDetailId = $request->get('hunt_user_details_id');
        
        $huntUser = HuntUser::with(['hunt_user_details'=>function($query) use ($huntUserDetailId){
                                $query->where('_id',$huntUserDetailId)
                                    ->with('game:_id,name,identifier','game_variation:_id,variation_name');
                            }])
                            ->where('user_id',$user->id)
                            ->first();


        $data = [
                    'game' => $huntUser->hunt_user_details[0]->game,
                    'game_variation' => $huntUser->hunt_user_details[0]->game_variation,
                ];

        return response()->json([
                                'status'  => true,
                                'message' => 'Clue game has been retrieved successfully',
                                'data'    => $data
                            ]);
    }
}
