<?php

namespace App\Console\Commands;

use App\CourseStudent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FreeTrialChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trials:check';

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
        Log::info('CHECKING TRIALS');

        CourseStudent::where('status', 'active')
            ->where('is_in_trial', true)
            ->get()->each(function ($student) {

                $now = Carbon::now();
                $trial_start_date = $student->trial_started;
                $diff = $now->diffInHours($trial_start_date);

                if ($diff > 72) $student->update(['status' => 'deactive', 'is_in_trial' => false]);

                // TODO: MAIL AND NOTIFY STUDENT
            });
    }
}
