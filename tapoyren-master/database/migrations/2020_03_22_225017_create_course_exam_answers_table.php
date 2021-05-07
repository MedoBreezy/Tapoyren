<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamAnswersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_exam_answers', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_exam_question_id');

         $table->mediumText('title');

         $table->foreign('course_exam_question_id')->references('id')->on('course_exam_questions');

         $table->softDeletes();
         $table->timestamps();
      });

      Schema::table('course_exam_questions', function (Blueprint $table) {
         $table->foreign('singleChoiceAnswerId')->references('id')->on('course_exam_answers');
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('course_exam_answers');
   }
}
