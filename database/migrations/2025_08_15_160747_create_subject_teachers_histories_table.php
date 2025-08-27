<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectTeachersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_teachers_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('questionnaire_contents_id')->nullable();
            $table->string('school_year', 2)->nullable()->comment('学年');
            $table->string('classification_code_class', 4)->nullable()->comment('クラス');
            $table->string('item_no_class', 2)->nullable();
            $table->integer('user_id')->nullable()->comment('教師ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_teachers_histories');
    }
}
