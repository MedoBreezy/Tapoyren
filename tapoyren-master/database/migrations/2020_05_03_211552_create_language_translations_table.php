<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageTranslationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('language_translations', function (Blueprint $table) {
         $table->bigIncrements('id');

         $table->unsignedBigInteger('language_id');
         $table->string('key');
         $table->mediumText('value');
         $table->enum('type', ['string', 'text'])->default('string');

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
      Schema::dropIfExists('language_translations');
   }
}
