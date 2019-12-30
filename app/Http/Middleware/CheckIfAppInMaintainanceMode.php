<?php

namespace App\Http\Middleware;

use App\Exceptions\AppInMaintenanceException;
use App\Repositories\AppStatisticRepository;
use Closure;

class CheckIfAppInMaintainanceMode
{

	protected $serverAppInfo;

	public function handle($request, Closure $next)
	{
		$this->serverAppInfo = (new AppStatisticRepository)->first(['id', 'maintenance']);
		if ($this->serverAppInfo->maintenance) {
			return response()->json(['message' => 'Server is under maintenance mode.'], 503);
		}
        return $next($request);
	}
}
