<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteSchoolBuildingToQuestionnaireScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_scores', function (Blueprint $table) {
            //
             $table->dropColumn('school_building_id');
            //   $table->string('school_building_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_scores', function (Blueprint $table) {
            //
        });
    }
}
