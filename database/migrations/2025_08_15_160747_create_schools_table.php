<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->nullable()->comment('学校名');
            $table->string('name_short', 10)->nullable()->comment('略称');
            $table->string('school_classification', 2)->nullable()->comment('学校区分:const.phpのschool_classification参照');
            $table->string('university_classification', 2)->nullable()->comment('国立・私立・公立区分:const.phpのuniversity_classification参照');
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
        Schema::dropIfExists('schools');
    }
}
