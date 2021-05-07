<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagePaymentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('package_payments', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('package_id');
         $table->unsignedBigInteger('student_id');

         $table->string('transaction_id');
         $table->enum('subscription_type', ['monthly', 'quarterly', 'semi_annually', 'annually'])->nullable();
         $table->enum('status', ['pending', 'canceled', 'completed'])->default('pending');
         $table->unsignedDecimal('price', 8, 2);

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
      Schema::dropIfExists('package_payments');
   }
}
