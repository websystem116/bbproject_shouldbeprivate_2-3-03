<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryConfirmationToDailySalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_salaries', function (Blueprint $table) {
            $table->boolean('salary_confirmation')->default(false)->comment("給与確認");  // カラム追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_salaries', function (Blueprint $table) {
            //
        });
    }
}
