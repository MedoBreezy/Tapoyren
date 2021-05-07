<?php

namespace App\Providers;

use App\Course;
use App\CoursePayment;
use App\CourseStudent;
use App\Mail\CoursePaymentNeededMail;
use App\Package;
use App\PackageSubscriber;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class PaymentDateCheckerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment() === 'production') {

            CourseStudent::where('status', 'active')
                ->where('is_in_trial', false)
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

            PackageSubscriber::where('status', 'active')->get()->each(function ($subscriber) {

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
}
