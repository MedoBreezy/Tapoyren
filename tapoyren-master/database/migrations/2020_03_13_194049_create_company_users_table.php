<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyUsersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('company_users', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('company_id');
         $table->unsignedBigInteger('user_id');

         $table->foreign('company_id')->references('id')->on('companies');
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
      Schema::dropIfExists('company_users');
   }
}
