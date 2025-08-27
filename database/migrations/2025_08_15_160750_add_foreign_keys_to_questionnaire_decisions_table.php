<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToQuestionnaireDecisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_decisions', function (Blueprint $table) {
            $table->foreign(['questionnaire_contents_id'])->references(['id'])->on('questionnaire_contents')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_decisions', function (Blueprint $table) {
            $table->dropForeign('questionnaire_decisions_questionnaire_contents_id_foreign');
        });
    }
}
