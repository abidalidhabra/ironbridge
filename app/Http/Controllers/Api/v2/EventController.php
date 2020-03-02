<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Models\v3\Event;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class EventController extends Controller
{
    
    public function getLeadersBoard(Request $request)
    {
    	$event = Event::running()
    			->whereHas('participations', function($query){
	    			$query->where('user_id', auth()->user()->id);
	    		})
	    		->select('id', 'name')
	    		->first();
    	if ($event) {
    		$data = (new UserRepository)->getModel()->whereHas('events', function($query) use ($event){
    			$query->where('event_id', $event->id);
    		})
    		->select('_id', 'first_name', 'last_name', 'compasses')
    		->orderBy('compasses.remaining', 1)
    		->get();
    	}
        return ResponseHelpers::successResponse($data ?? []);
    }
}
