<?php

namespace App\Console\Commands;

use App\Services\Event\EventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartAnEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start the events.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $eventService = (new EventService)->participate();
        Log::info(json_encode($eventService->response()).' event:start Cummand Run successfully! at '. now()->setTimeZone('Asia/Kolkata')->format('d-m-Y h:i:s A'));
    }
}
