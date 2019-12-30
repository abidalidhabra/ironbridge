<?php

namespace App\Http\Middleware;

use App\Exceptions\AppInMaintenanceException;
use App\Repositories\AppStatisticRepository;
use Closure;

class CheckIfAppIsNotUpdated
{

	protected $serverInfo;

	public function handle($request, Closure $next)
	{
		$this->serverInfo = (new AppStatisticRepository)->first(['id', 'app_versions']);
		if (
			($request->device_type == 'android' && $this->serverInfo->app_versions['android'] > (float)$request->app_version) ||
			($request->device_type == 'ios' && $this->serverInfo->app_versions['ios'] > (float)$request->app_version)
		) {
			return response()->json(['code'=> 14, 'message' => 'Please update an application.'], 500);
		}
        return $next($request);
	}
}
