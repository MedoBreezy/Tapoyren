<?php

namespace App\Console\Commands;

use App\Course;
use App\CourseStudent;
use App\Mail\CoursePaymentNeededMail;
use App\Package;
use App\PackageSubscriber;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check';

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
        Log::info('CHECKING PAYMENTS');
        $now = Carbon::now();

        CourseStudent::where('status', 'active')
            ->where('is_in_trial', false)
            ->where('next_payment_date', '<=', $now)
            ->where('subscription', '!=', null)->get()->each(function ($student) {

                $nextPaymentDate = Carbon::parse($student->next_payment_date);

                if ($nextPaymentDate->isPast()) {
                    $student->update(['status' => 'deactive']);

                    $user = User::find($student->student_id);
                    $course = Course::find($student->course_id);

                    Mail::to($user->email)->send(new CoursePaymentNeededMail($user, $course));

                    //
                }
                //
            });

        PackageSubscriber::where('status', 'active')
            ->where('next_payment_date', '<=', $now)
            ->get()->each(function ($subscriber) {

                $nextPaymentDate = Carbon::parse($subscriber->next_payment_date);

                if ($nextPaymentDate->isPast()) {
                    $user = User::find($subscriber->student_id);
                    $package = Package::find($subscriber->package_id);

                    $subscriber->update(['status' => 'deactive']);


                    CourseStudent::where('by_package_id', $package->id)
                        ->where('student_id', $subscriber->student_id)->get()->each(function ($packageCourseStudent) {
                            $packageCourseStudent->update(['status' => 'deactive']);
                        });

                    Mail::to($user->email)->send(new CoursePaymentNeededMail($user, $package));

                    //
                }
                //
            });
    }
}
