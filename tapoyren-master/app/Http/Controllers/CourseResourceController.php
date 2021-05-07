<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseVideo;
use App\CourseVideoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseResourceController extends Controller
{
   public function view_course(Request $request, Course $course)
   {
      return view('admin.course.resources.view_lessons')->with([
         'course' => $course
      ]);
   }

   public function view_lesson_resource_add(Request $request, Course $course, CourseVideo $lesson)
   {
      return view('admin.course.resources.lesson_resource_add')->with([
         'course' => $course,
         'lesson' => $lesson
      ]);
   }

   public function lesson_resource_add(Request $request, Course $course, CourseVideo $lesson)
   {
      $resources = json_decode($request->resources);


      foreach ($resources as $resource) {
         $newResource = CourseVideoResource::create([
            'course_video_id' => $lesson->id,
            'title' => $resource->title,
            'file_url' => $resource->file
         ]);
      }

      return response()->json(['code' => 'done']);
   }

   //
}
