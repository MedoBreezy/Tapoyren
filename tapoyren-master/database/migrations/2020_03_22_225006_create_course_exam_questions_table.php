<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamQuestionsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_exam_questions', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_exam_id');
         $table->unsignedBigInteger('relatedLectureId');

         $table->enum('answer_type', ['single_choice', 'multiple_choice', 'assignment']);

         $table->mediumText('title');
         $table->string('topic')->nullable();
         $table->mediumText('explanation')->nullable();
         $table->string('question_vimeoVideoId')->nullable();
         $table->string('explanation_vimeoVideoId')->nullable();

         $table->unsignedBigInteger('singleChoiceAnswerId')->nullable();
         $table->json('multipleChoiceAnswerIds')->nullable();
         $table->string('assignmentAnswer')->nullable();

         $table->foreign('course_exam_id')->references('id')->on('course_exams');
         $table->foreign('relatedLectureId')->references('id')->on('course_videos');

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
      Schema::dropIfExists('course_exam_questions');
   }
}
