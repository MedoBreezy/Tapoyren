<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamDataTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_exam_data', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_exam_id');
         $table->unsignedBigInteger('student_id');

         $table->unsignedInteger('points')->default(0);
         $table->unsignedInteger('total_exam_time')->default(0);

         $table->enum('status', ['passed', 'failed']);

         $table->foreign('course_exam_id')->references('id')->on('course_exams');
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
      Schema::dropIfExists('course_exam_data');
   }
}
