<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseRequest;

class CheckRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $requests = CourseRequest::all()->where('status', 'active')->where('expiration_date', '<=', now());

        foreach ($requests as $request) {
            $request->status = 'expired';
            $request->authorized = false;
            $request->save();
        }
      
        \Log::info("Daily request analysis done.");
        $this->info('Demo:Cron Command Run successfully!');
    }
}
