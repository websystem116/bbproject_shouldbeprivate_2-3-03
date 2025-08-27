<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumn2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_results_details', function (Blueprint $table) {
            //add column
            $table->integer('alphabet_id_6')->nullable()->after('question_7_5');
            $table->integer('subject_id_6')->nullable()->after('question_7_5');
            $table->integer('user_id_6')->nullable()->after('question_7_5');
            $table->integer('question_1_6')->nullable()->after('question_7_5');
            $table->integer('question_2_6')->nullable()->after('question_7_5');
            $table->integer('question_3_6')->nullable()->after('question_7_5');
            $table->integer('question_4_6')->nullable()->after('question_7_5');
            $table->integer('question_5_6')->nullable()->after('question_7_5');
            $table->integer('question_6_6')->nullable()->after('question_7_5');
            $table->integer('question_7_6')->nullable()->after('question_7_5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_results_details', function (Blueprint $table) {
            //
        });
    }
}
