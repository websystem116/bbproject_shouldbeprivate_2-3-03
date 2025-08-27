<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHighschoolCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('highschool_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_id', 4)->nullable()->comment('学校マスタid');
            $table->string('name', 20)->nullable()->comment('名称');
            $table->string('name_short', 10)->nullable()->comment('略称');
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
        Schema::dropIfExists('highschool_courses');
    }
}
