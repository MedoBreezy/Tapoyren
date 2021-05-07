<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseDiscussionMessagesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_discussion_messages', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_discussion_id');
         $table->unsignedBigInteger('parent_message_id')->nullable();
         $table->unsignedBigInteger('user_id');
         $table->string('title')->nullable();
         $table->mediumText('question');

         $table->foreign('course_discussion_id')->references('id')->on('course_discussions');
         $table->foreign('parent_message_id')->references('id')->on('course_discussion_messages');
         $table->foreign('user_id')->references('id')->on('users');

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
      Schema::dropIfExists('course_discussion_messages');
   }
}
