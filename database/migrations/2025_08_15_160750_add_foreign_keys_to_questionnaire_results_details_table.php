<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToQuestionnaireResultsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_results_details', function (Blueprint $table) {
            $table->foreign(['questionnaire_content_id'])->references(['id'])->on('questionnaire_contents')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['school_building_id'])->references(['id'])->on('school_buildings')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
            $table->dropForeign('questionnaire_results_details_questionnaire_content_id_foreign');
            $table->dropForeign('questionnaire_results_details_school_building_id_foreign');
        });
    }
}
