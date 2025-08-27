<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireEverySubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_every_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('questionnaire_results_details_id')->nullable()->comment('questionnaire_results_detailsのid');
            $table->integer('alphabet_id')->nullable()->comment('クラス:const.phpのalphabets参照');
            $table->integer('subject_id')->nullable()->comment('教科:const.phpのsubjects参照');
            $table->integer('user_id')->nullable()->comment('教師のID');
            $table->integer('question1')->nullable()->comment('質問1');
            $table->integer('question2')->nullable()->comment('質問2');
            $table->integer('question3')->nullable()->comment('質問3');
            $table->integer('question4')->nullable()->comment('質問4');
            $table->integer('question5')->nullable()->comment('質問5');
            $table->integer('question6')->nullable()->comment('質問6');
            $table->integer('question7')->nullable()->comment('質問7');
            $table->integer('Creator')->nullable();
            $table->integer('Updater')->nullable();
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
        Schema::dropIfExists('questionnaire_every_subjects');
    }
}
