<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('notifications', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('to_user_id');
         $table->mediumText('notification');
         $table->boolean('read')->default(false);
         $table->string('link')->nullable();

         $table->foreign('to_user_id')->references('id')->on('users');

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
      Schema::dropIfExists('notifications');
   }
}
