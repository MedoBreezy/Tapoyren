<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseResourcesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_resources', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('title');
         $table->string('file_url');

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
      Schema::dropIfExists('course_resources');
   }
}
