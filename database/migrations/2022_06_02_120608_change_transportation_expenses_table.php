<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTransportationExpensesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('transportation_expenses', function (Blueprint $table) {
			$table->string('daily_salary_id');  //カラム追加
			// $table->string('salary_id');  //カラム追加
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('transportation_expenses', function (Blueprint $table) {
			//
		});
	}
}
