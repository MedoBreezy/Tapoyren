<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('users', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->string('name');
         $table->string('email')->unique();
         $table->mediumText('about')->nullable();
         $table->enum('gender', ['male', 'female'])->default('male');
         $table->date('birthDate');
         $table->integer('registration_number')->nullable();
         $table->enum('employment_status', [1, 2, 3, 4])->nullable();
         $table->enum('type', ['student', 'instructor', 'admin'])->default('student');
         $table->timestamp('email_verified_at')->nullable();
         $table->string('api_token')->nullable();
         $table->string('avatar_url')->nullable();
         $table->mediumText('bio')->nullable();
         $table->string('password');
         $table->rememberToken();
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
      Schema::dropIfExists('users');
   }
}
