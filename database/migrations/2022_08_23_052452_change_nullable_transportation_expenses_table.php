<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableTransportationExpensesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('transportation_expenses', function (Blueprint $table) {
			$table->string('daily_salary_id')->nullable()->change();
			$table->string('salary_id')->nullable()->change();
			$table->string('remarks')->nullable()->change();
			$table->string('superior_approval')->nullable()->change();
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
