<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseVideoWatchedsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_video_watcheds', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_id');
         $table->unsignedBigInteger('lesson_id');
         $table->unsignedBigInteger('student_id');

         $table->foreign('course_id')->references('id')->on('courses');
         $table->foreign('lesson_id')->references('id')->on('course_videos');
         $table->foreign('student_id')->references('id')->on('users');

         $table->softDeletes();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('course_video_watcheds');
   }
}
