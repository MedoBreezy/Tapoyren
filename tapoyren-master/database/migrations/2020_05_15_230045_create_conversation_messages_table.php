<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationMessagesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('conversation_messages', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('conversation_id');
         $table->string('message');

         $table->enum('sender', ['student', 'instructor']);

         $table->foreign('conversation_id')->references('id')->on('conversations');

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
      Schema::dropIfExists('conversation_messages');
   }
}
