<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\v1\Game;
use App\Models\v1\User;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId as MongoDBId;
use MongoDB\BSON\UTCDateTime as MongoDBDate;
use Exception;

class MGController extends Controller
{
    
    public function getGamesData(Request $request)
    {
    	try {
    		
    		$data = Game::active()->select('_id','name','identifier')->with('practice_games_targets:_id,game_id,target,variation_image')->get();
    		return response()->json(['message'=> 'Target info of each game retrieved successfully.', 'data'=> $data]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }

    public function addSkeletonKey(Request $request)
    {
    	try {
    		
	    	$skeletonKey[] = [
	    		'key'       => new MongoDBId(),
	    		'created_at'=> new MongoDBDate(),
	    		'used_at'   => null
	    	];
	    	$user = auth()->user();
	    	$user->push('skeleton_keys', $skeletonKey);
	    	return response()->json(['message'=> 'Skeleton key has been added successfully to your account.', 'available_skeleton_keys'=> $user->available_skeleton_keys]);
    	} catch (Exception $e) {
    		return response()->json(['message'=> $e->getMessage()], 500);
    	}
    }
}
