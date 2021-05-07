<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqQuestionTranslationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('faq_question_translations', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('faq_question_id');
         $table->unsignedBigInteger('language_id');

         $table->string('question');
         $table->text('description');

         $table->foreign('faq_question_id')->references('id')->on('faq_questions');
         $table->foreign('language_id')->references('id')->on('languages');

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
      Schema::dropIfExists('faq_question_translations');
   }
}
