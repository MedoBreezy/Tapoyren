<?php

namespace App\Http\Middleware;

use App\Course;
use App\CourseStudent as AppCourseStudent;
use App\CourseVideo;
use App\Package;
use App\PackageCourse;
use App\PackageSubscriber;
use Closure;

class CourseStudent
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle($request, Closure $next)
   {
      $parameters = $request->route()->parameters();

      if (isset($parameters['course']) && $parameters['course']->status === 'active' && $parameters['course'] instanceof Course) {

         $course = $parameters['course'];
         $userId = auth()->user()->id;

         $checkStudent = AppCourseStudent::where('course_id', $course->id)
            ->where('student_id', $userId)->where('status','active')->count() > 0;
            // 

         if ($checkStudent) return $next($request);
         else return redirect('course/' . $parameters['course']->id . '/enroll');
      } else abort(403);
   }
}
