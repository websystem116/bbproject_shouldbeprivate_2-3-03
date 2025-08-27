<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_types', function (Blueprint $table) {
            $table->increments('id')->comment('コース種別ID');
            $table->integer('course_id')->nullable()->comment('コースマスターID');
            $table->string('type_name', 128)->nullable()->comment('コース種別名');
            $table->tinyInteger('show_pulldown')->nullable()->default('0')->comment('回数のプルダウン表示');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_types');
    }
}
