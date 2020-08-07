<?php

namespace App\Jobs;

use App\Notifications\EventNotification;
use App\Repositories\AppStatisticRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use MongoDB\BSON\UTCDateTime;

class SendPushToInactiveUsers
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $appSetting = (new AppStatisticRepository)->first(['_id', 'inactivity_notification']);
        if ($appSetting['inactivity_notification']['active']) {
            $minDate = new UTCDateTime(now()->subHours($appSetting['inactivity_notification']['when']));
            $users = (new UserRepository)->getModel()
                    ->where('additional.last_login_at', '<=', $minDate)
                    ->select('_id','first_name','last_name','firebase_ids','device_info')
                    ->get();
            Notification::send($users, new EventNotification('Ironbridge', $appSetting->inactivity_notification['message']));
        }
    }
}
