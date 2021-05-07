<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('messages', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('sender_user_id');
         $table->unsignedBigInteger('to_user_id');
         $table->mediumText('message');
         $table->boolean('read')->default(false);

         $table->foreign('sender_user_id')->references('id')->on('users');
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
      Schema::dropIfExists('messages');
   }
}
