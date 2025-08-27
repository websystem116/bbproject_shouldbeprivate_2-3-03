<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id')->comment('コースマスターID');
            $table->tinyInteger('brand')->nullable()->comment('ブランド');
            $table->string('name', 128)->nullable()->comment('コース名');
            $table->tinyInteger('from_grade')->nullable()->comment('開始学年');
            $table->tinyInteger('to_grade')->nullable()->comment('終了学年');
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
        Schema::dropIfExists('courses');
    }
}
