<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseStudentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_students', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_id');
         $table->unsignedBigInteger('student_id');
         $table->unsignedBigInteger('last_lesson_id');
         $table->unsignedBigInteger('by_package_id')->nullable();

         $table->boolean('is_in_trial')->default(false);
         $table->timestamp('trial_started')->nullable();

         $table->timestamp('last_payment_date')->nullable();
         $table->timestamp('next_payment_date')->nullable();

         $table->enum('status', ['active', 'deactive'])->default('active');
         $table->enum('subscription', ['monthly', 'quarterly', 'semi_annually', 'annually'])->nullable();

         $table->foreign('course_id')->references('id')->on('courses');
         $table->foreign('student_id')->references('id')->on('users');
         $table->foreign('last_lesson_id')->references('id')->on('course_videos');
         $table->foreign('by_package_id')->references('id')->on('packages');

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
      Schema::dropIfExists('course_students');
   }
}
