<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseStudentsByCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_students', function (Blueprint $table) {
            $table->unsignedBigInteger('by_company_id')->nullable();
            $table->foreign('by_company_id')->references('id')->on('companies')->onDelete('cascade');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_students', function (Blueprint $table) {
            //
        });
    }
}
