<?php

namespace App\Console\Commands;

use App\Services\Event\EventService;
use App\Services\Event\ParticipateInEvent as ParticipateInEventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ParticipateInEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:participate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This participates the users in their home town\'s events';

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
        (new EventService)->participate();
        Log::info('event:participate Cummand Run successfully! at '. now()->setTimeZone('Asia/Kolkata')->format('d-m-Y h:i:s A'));
    }
}
