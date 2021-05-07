<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResetPasswordTokensTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('reset_password_tokens', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('user_id');
         $table->string('token');

         $table->enum('status', ['pending', 'expired'])->default('pending');

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
      Schema::dropIfExists('reset_password_tokens');
   }
}
