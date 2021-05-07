<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyEmailsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('verify_emails', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('user_id');
         $table->enum('status', ['pending', 'activated']);
         $table->string('token');

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
      Schema::dropIfExists('verify_emails');
   }
}
