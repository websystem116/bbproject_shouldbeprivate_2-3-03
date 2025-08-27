<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableDailySalariesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('daily_salaries', function (Blueprint $table) {
			$table->string('salary_id')->nullable()->change();
			$table->integer('school_year')->nullable()->change();
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
		Schema::table('daily_salaries', function (Blueprint $table) {
			//
		});
	}
}
