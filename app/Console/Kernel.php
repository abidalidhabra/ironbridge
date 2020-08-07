<?php

namespace App\Console;

use App\Console\Commands\FinishAnEvent;
use App\Console\Commands\StartAnEvent;
use App\Jobs\SendPushToInactiveUsers;
use App\Services\Event\EventNotificationService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StartAnEvent::class,
        FinishAnEvent::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->command('event:start')->everyMinute();
        $schedule->command('event:finish')->everyMinute();
        $schedule->job(new EventNotificationService)->everyMinute();
        $schedule->job(new SendPushToInactiveUsers)->twiceDaily(0, 18);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
