<?php

namespace App\Http\Middleware;

use App\Exceptions\AppInMaintenanceException;
use App\Repositories\AppStatisticRepository;
use Carbon\Carbon;
use Closure;

class CheckIfAppInMaintainanceMode
{

	protected $serverAppInfo;

	public function handle($request, Closure $next)
	{
		$this->serverAppInfo = (new AppStatisticRepository)->first(['id', 'maintenance', 'maintenance_time']);
		if (
			// $this->serverAppInfo->maintenance || 
			(
				$this->serverAppInfo->maintenance_time && 
				Carbon::parse($this->serverAppInfo->maintenance_time['start']->toDateTime()) <= now() && 
				Carbon::parse($this->serverAppInfo->maintenance_time['end']->toDateTime()) >= now()
			)
		) {
			return response()->json([
				'message' => 'Server is under maintenance mode.', 
				'start'=> $this->serverAppInfo->maintenance_time['start']->toDateTime()->format('Y-m-d H:i:s'),
				'end'=> $this->serverAppInfo->maintenance_time['end']->toDateTime()->format('Y-m-d H:i:s')
			], 503);
		}
        return $next($request);
	}
}
