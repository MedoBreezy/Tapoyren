<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseVideosTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_videos', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('title');
         $table->string('vimeoVideoId');
         $table->unsignedInteger('timescale');

         $table->boolean('preview')->default(false);

         $table->unsignedBigInteger('section_id');

         $table->foreign('section_id')->references('id')->on('course_sections');

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
      Schema::dropIfExists('course_videos');
   }
}
