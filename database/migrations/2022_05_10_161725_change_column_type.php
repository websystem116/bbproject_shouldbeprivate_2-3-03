<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_scores', function (Blueprint $table) {
            // 型を小数点へ変更
            $table->float('classroom_score')->change();
            $table->float('subject_score')->change();

            // $table->double('classroom_score')->change();
            // $table->double('subject_score')->change();
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
        });
    }
}
