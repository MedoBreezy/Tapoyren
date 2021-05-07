<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('packages', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->string('name');
         $table->mediumText('description');
         $table->string('thumbnail_url');

         $table->enum('status', ['active', 'deactive'])->default('deactive');

         $table->unsignedDecimal('price_monthly', 8, 2)->nullable();
         $table->unsignedDecimal('price_quarterly', 8, 2)->nullable();
         $table->unsignedDecimal('price_semiannually', 8, 2)->nullable();
         $table->unsignedDecimal('price_annually', 8, 2)->nullable();

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
      Schema::dropIfExists('packages');
   }
}
