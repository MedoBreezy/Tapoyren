<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTranslationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('category_translations', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('category_id');
         $table->unsignedBigInteger('language_id');

         $table->string('title');

         $table->foreign('category_id')->references('id')->on('categories');
         $table->foreign('language_id')->references('id')->on('languages');

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
      Schema::dropIfExists('category_translations');
   }
}
