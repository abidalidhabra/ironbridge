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
		$user = auth('api')->user();
		if (
			($user->device_info['type'] == 'android' && $this->serverInfo->app_versions['android'] > $user->additional['app_version']) ||
			($user->device_info['type'] == 'ios' && $this->serverInfo->app_versions['ios'] > $user->additional['app_version'])
		) {
			return response()->json(['code'=> 14, 'message' => 'Please update an application.'], 500);
		}
        return $next($request);
	}
}
