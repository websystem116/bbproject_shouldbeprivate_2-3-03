<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireDecisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_decisions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('questionnaire_contents_id')->nullable()->index('questionnaire_decisions_questionnaire_contents_id_foreign')->comment('questionnaire_contentsテーブルのid');
            $table->integer('user_id')->nullable()->comment('usersテーブルのid');
            $table->double('classroom_score')->nullable()->comment('教室補正値');
            $table->double('subject_score')->nullable()->comment('教科補正値');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire_decisions');
    }
}
