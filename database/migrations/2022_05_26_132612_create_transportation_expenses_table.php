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
			$table->string('user_id', 20)->comment('ユーザーID');
			$table->string('salary_id', 20)->comment('給与テーブルID');

			$table->string('work_month', 20)->comment('年月');
			$table->string('work_date', 20)->comment('出勤日');
			$table->integer('school_building')->comment('校舎No');
			$table->string('route', 40)->comment('路線名');
			$table->string('boarding_station', 40)->comment('乗車駅');
			$table->string('get_off_station', 40)->comment('降車駅');
			$table->integer('unit_price')->comment('単価');
			$table->integer('round_trip_flg')->comment('往復フラグ');
			$table->integer('fare')->comment('運賃');
			$table->string('remarks')->comment('備考');
			$table->integer('superior_approval')->comment('上長承認');
			$table->integer('creator')->comment('登録者');
			$table->integer('updater')->comment('更新者');

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
		Schema::dropIfExists('transportation_expenses');
	}
}
