<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireResultsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_results_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('management_code', 15)->nullable()->comment('アンケートNo');
            $table->unsignedInteger('questionnaire_content_id')->nullable()->index('questionnaire_results_details_questionnaire_content_id_foreign')->comment('questionnaire_contentsテーブルのid');
            $table->unsignedInteger('school_building_id')->nullable()->index('questionnaire_results_details_school_building_id_foreign')->comment('school_buildingsのid');
            $table->integer('school_year_id')->nullable()->comment('const.phpのschool_year参照');
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
        Schema::dropIfExists('questionnaire_results_details');
    }
}
