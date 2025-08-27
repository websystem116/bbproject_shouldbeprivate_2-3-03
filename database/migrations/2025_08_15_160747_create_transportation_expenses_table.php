<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportationExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transportation_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id', 20)->nullable()->comment('ユーザーID');
            $table->string('work_month', 20)->nullable()->comment('年月');
            $table->string('work_date', 20)->nullable()->comment('出勤日');
            $table->integer('school_building')->nullable()->comment('校舎No');
            $table->string('route', 40)->nullable()->comment('路線名');
            $table->string('boarding_station', 40)->nullable()->comment('乗車駅');
            $table->string('get_off_station', 40)->nullable()->comment('降車駅');
            $table->integer('unit_price')->nullable()->comment('単価');
            $table->integer('round_trip_flg')->nullable()->comment('往復フラグ');
            $table->integer('fare')->nullable()->comment('運賃');
            $table->string('remarks')->nullable()->comment('備考');
            $table->string('superior_approval')->nullable()->comment('上長承認');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->timestamps();
            $table->string('daily_salary_id')->nullable();
            $table->string('salary_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transportation_expenses');
    }
}
