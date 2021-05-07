<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_exams', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_id');
         $table->unsignedBigInteger('order_lecture_id')->nullable();

         $table->string('title');
         $table->mediumText('description');

         $table->unsignedInteger('minimum_point');

         $table->enum('type', ['time', 'timeless']);
         $table->unsignedInteger('time')->nullable();

         $table->enum('status', ['active', 'deactive'])->default('deactive');

         $table->foreign('course_id')->references('id')->on('courses');
         $table->foreign('order_lecture_id')->references('id')->on('course_videos');

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
      Schema::dropIfExists('course_exams');
   }
}
