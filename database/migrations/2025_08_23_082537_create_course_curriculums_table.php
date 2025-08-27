<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseCurriculumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_curriculums', function (Blueprint $table) {
            $table->increments('id')->comment('コース提供教科ID');
            $table->integer('course_id')->nullable()->comment('コースマスターID');
            $table->integer('curriculum_id')->nullable()->comment('教科マスタID');
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
        Schema::dropIfExists('course_curriculums');
    }
}
