<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageSubscribersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('package_subscribers', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('package_id');
         $table->unsignedBigInteger('student_id');

         $table->timestamp('last_payment_date')->nullable();
         $table->timestamp('next_payment_date')->nullable();

         $table->enum('status', ['active', 'deactive'])->default('active');
         $table->enum('subscription', ['monthly', 'quarterly', 'semi_annually', 'annually'])->nullable();

         $table->foreign('package_id')->references('id')->on('packages');
         $table->foreign('student_id')->references('id')->on('users');

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
      Schema::dropIfExists('package_subscribers');
   }
}
