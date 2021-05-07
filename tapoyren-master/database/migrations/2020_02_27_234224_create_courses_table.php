<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('courses', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('title');
         $table->string('description');
         $table->string('slug');
         $table->text('about');
         $table->decimal('rating', 2, 1)->default(5.0);
         $table->string('language');
         $table->unsignedInteger('timescale');
         $table->unsignedBigInteger('view_count')->default(0);
         $table->boolean('has_trial')->default(false);
         $table->enum('type', ['course', 'subcourse'])->default('course');
         $table->enum('difficulty', [0, 1, 2])->default(0);
         $table->mediumText('startCourseMail')->nullable();
         $table->mediumText('finishCourseMail')->nullable();
         $table->string('thumbnail_url');
         $table->enum('status', ['pending', 'active'])->default('pending');
         $table->enum('price_type', ['free', 'paid'])->default('free');
         $table->unsignedDecimal('price_monthly', 8, 2)->nullable();
         $table->unsignedDecimal('price_quarterly', 8, 2)->nullable();
         $table->unsignedDecimal('price_semiannually', 8, 2)->nullable();
         $table->unsignedDecimal('price_annually', 8, 2)->nullable();

         $table->unsignedBigInteger('category_id');
         $table->unsignedBigInteger('instructor_id');
         $table->unsignedBigInteger('parent_course_id')->nullable();

         $table->foreign('category_id')->references('id')->on('categories');
         $table->foreign('instructor_id')->references('id')->on('users');
         $table->foreign('parent_course_id')->references('id')->on('courses');

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
      Schema::dropIfExists('courses');
   }
}
