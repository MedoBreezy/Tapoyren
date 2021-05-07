<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseFreeTrialsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_free_trials', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('course_id');
         $table->unsignedBigInteger('student_id');
         $table->enum('status', ['active', 'expired'])->default('active');
         $table->enum('subscription', ['monthly', 'quarterly', 'semi_annually', 'annually']);

         $table->foreign('course_id')->references('id')->on('courses');
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
      Schema::dropIfExists('course_free_trials');
   }
}
