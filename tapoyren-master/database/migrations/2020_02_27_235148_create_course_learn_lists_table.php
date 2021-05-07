<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLearnListsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_learn_lists', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('title');

         $table->unsignedBigInteger('course_id');

         $table->foreign('course_id')->references('id')->on('courses');

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
      Schema::dropIfExists('course_learn_lists');
   }
}
