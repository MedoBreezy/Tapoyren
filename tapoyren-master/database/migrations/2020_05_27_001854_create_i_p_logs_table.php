<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIPLogsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('i_p_logs', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('user_id');
         $table->string('session_id')->nullable();
         $table->string('ip');
         $table->string('path')->nullable();
         $table->unsignedBigInteger('time');

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
      Schema::dropIfExists('i_p_logs');
   }
}
