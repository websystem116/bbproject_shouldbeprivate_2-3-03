<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 30)->comment('タイトル');
            $table->string('summary', 200)->nullable()->comment('概要');
            $table->string('month', 7)->nullable()->comment('年月');
            $table->string('question1', 40)->nullable()->comment('未使用');
            $table->string('question1_compensation', 10)->nullable()->comment('質問1補正値');
            $table->string('question1_choice1', 40)->nullable()->comment('未使用');
            $table->string('question1_choice2', 40)->nullable()->comment('未使用');
            $table->string('question1_choice3', 40)->nullable()->comment('未使用');
            $table->string('question1_choice4', 40)->nullable()->comment('未使用');
            $table->string('question2', 40)->nullable()->comment('未使用');
            $table->string('question2_compensation', 10)->nullable()->comment('質問2補正値');
            $table->string('question2_choice1', 40)->nullable()->comment('未使用');
            $table->string('question2_choice2', 40)->nullable()->comment('未使用');
            $table->string('question2_choice3', 40)->nullable()->comment('未使用');
            $table->string('question2_choice4', 40)->nullable()->comment('未使用');
            $table->string('question3', 40)->nullable()->comment('未使用');
            $table->string('question3_compensation', 10)->nullable()->comment('質問3補正値');
            $table->string('question3_choice1', 40)->nullable()->comment('未使用');
            $table->string('question3_choice2', 40)->nullable()->comment('未使用');
            $table->string('question3_choice3', 40)->nullable()->comment('未使用');
            $table->string('question3_choice4', 40)->nullable()->comment('未使用');
            $table->string('question4', 40)->nullable()->comment('未使用');
            $table->string('question4_compensation', 10)->nullable()->comment('質問4補正値');
            $table->string('question4_choice1', 40)->nullable()->comment('未使用');
            $table->string('question4_choice2', 40)->nullable()->comment('未使用');
            $table->string('question4_choice3', 40)->nullable()->comment('未使用');
            $table->string('question4_choice4', 40)->nullable()->comment('未使用');
            $table->string('question5', 40)->nullable()->comment('未使用');
            $table->string('question5_compensation', 10)->nullable()->comment('質問5補正値');
            $table->string('question5_choice1', 40)->nullable()->comment('未使用');
            $table->string('question5_choice2', 40)->nullable()->comment('未使用');
            $table->string('question5_choice3', 40)->nullable()->comment('未使用');
            $table->string('question5_choice4', 40)->nullable()->comment('未使用');
            $table->string('question6', 40)->nullable()->comment('未使用');
            $table->string('question6_compensation', 10)->nullable()->comment('質問6補正値');
            $table->string('question6_choice1', 40)->nullable()->comment('未使用');
            $table->string('question6_choice2', 40)->nullable()->comment('未使用');
            $table->string('question6_choice3', 40)->nullable()->comment('未使用');
            $table->string('question6_choice4', 40)->nullable()->comment('未使用');
            $table->string('question7', 40)->nullable()->comment('未使用');
            $table->string('question7_compensation', 10)->nullable()->comment('質問7補正値');
            $table->string('question7_choice1', 40)->nullable()->comment('未使用');
            $table->string('question7_choice2', 40)->nullable()->comment('未使用');
            $table->string('question7_choice3', 40)->nullable()->comment('未使用');
            $table->string('question7_choice4', 40)->nullable()->comment('未使用');
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
        Schema::dropIfExists('questionnaire_contents');
    }
}
