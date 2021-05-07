<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageCoursesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('package_courses', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('package_id');
         $table->unsignedBigInteger('course_id');

         $table->foreign('package_id')->references('id')->on('packages');
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
      Schema::dropIfExists('package_courses');
   }
}
