<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnSubjectIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_results_details', function (Blueprint $table) {

            $table->dropColumn('subject_id_1');
            $table->dropColumn('subject_id_2');
            $table->dropColumn('subject_id_3');
            $table->dropColumn('subject_id_4');
            $table->dropColumn('subject_id_5');
            $table->dropColumn('subject_id_6');

            $table->dropColumn('user_id_1');
            $table->dropColumn('user_id_2');
            $table->dropColumn('user_id_3');
            $table->dropColumn('user_id_4');
            $table->dropColumn('user_id_5');
            $table->dropColumn('user_id_6');

            $table->dropColumn('question_1_1');
            $table->dropColumn('question_2_1');
            $table->dropColumn('question_3_1');
            $table->dropColumn('question_4_1');
            $table->dropColumn('question_5_1');
            $table->dropColumn('question_6_1');
            $table->dropColumn('question_7_1');

            $table->dropColumn('question_1_2');
            $table->dropColumn('question_2_2');
            $table->dropColumn('question_3_2');
            $table->dropColumn('question_4_2');
            $table->dropColumn('question_5_2');
            $table->dropColumn('question_6_2');
            $table->dropColumn('question_7_2');

            $table->dropColumn('question_1_3');
            $table->dropColumn('question_2_3');
            $table->dropColumn('question_3_3');
            $table->dropColumn('question_4_3');
            $table->dropColumn('question_5_3');
            $table->dropColumn('question_6_3');
            $table->dropColumn('question_7_3');

            $table->dropColumn('question_1_4');
            $table->dropColumn('question_2_4');
            $table->dropColumn('question_3_4');
            $table->dropColumn('question_4_4');
            $table->dropColumn('question_5_4');
            $table->dropColumn('question_6_4');
            $table->dropColumn('question_7_4');

            $table->dropColumn('question_1_5');
            $table->dropColumn('question_2_5');
            $table->dropColumn('question_3_5');
            $table->dropColumn('question_4_5');
            $table->dropColumn('question_5_5');
            $table->dropColumn('question_6_5');
            $table->dropColumn('question_7_5');

            $table->dropColumn('question_1_6');
            $table->dropColumn('question_2_6');
            $table->dropColumn('question_3_6');
            $table->dropColumn('question_4_6');
            $table->dropColumn('question_5_6');
            $table->dropColumn('question_6_6');
            $table->dropColumn('question_7_6');
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
