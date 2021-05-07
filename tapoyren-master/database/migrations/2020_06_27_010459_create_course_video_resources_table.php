<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseVideoResourcesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_video_resources', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('title');
         $table->string('file_url');

         $table->unsignedBigInteger('course_video_id');

         $table->foreign('course_video_id')->references('id')->on('course_videos');

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
      Schema::dropIfExists('course_video_resources');
   }
}
