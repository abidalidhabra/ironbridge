<?php

namespace App\Console\Commands;

use App\Services\Event\EventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinishAnEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:finish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command finish running events';

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
        (new EventService)->finish();
        Log::info('event:finish Cummand Run successfully! at '. now()->setTimeZone('Asia/Kolkata')->format('d-m-Y h:i:s A'));
    }
}
