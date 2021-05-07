<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('conversations', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('student_id');
         $table->unsignedBigInteger('instructor_id');

         $table->foreign('student_id')->references('id')->on('users');
         $table->foreign('instructor_id')->references('id')->on('users');

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
      Schema::dropIfExists('conversations');
   }
}
