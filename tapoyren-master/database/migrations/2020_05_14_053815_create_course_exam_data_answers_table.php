<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamDataAnswersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_exam_data_answers', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_exam_data_id');
         $table->unsignedBigInteger('course_exam_question_id');

         $table->enum('status', ['correct', 'incorrect']);
         $table->unsignedInteger('time')->default(0);

         $table->string('singleChoiceAnswerId')->nullable();
         $table->json('multipleChoiceAnswerIds')->nullable();
         $table->string('assignmentAnswer')->nullable();


         $table->foreign('course_exam_data_id')->references('id')->on('course_exam_data');
         $table->foreign('course_exam_question_id')->references('id')->on('course_exam_questions');

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
      Schema::dropIfExists('course_exam_data_answers');
   }
}
