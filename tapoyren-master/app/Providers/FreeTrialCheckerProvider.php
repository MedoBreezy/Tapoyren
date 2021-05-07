<?php

namespace App\Providers;

use App\CourseStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class FreeTrialCheckerProvider extends ServiceProvider
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
            ->where('is_in_trial', true)->get()->each(function ($student) {

               $now = Carbon::now();
               $trial_start_date = $student->trial_started;
               $diff = $now->diffInHours($trial_start_date);

               if ($diff > 72) $student->update(['status' => 'deactive', 'is_in_trial' => false]);
               Log::info("DIFF: {$diff}");
               // TODO: MAIL AND NOTIFY STUDENT

               //
            });
      }
   }
}
