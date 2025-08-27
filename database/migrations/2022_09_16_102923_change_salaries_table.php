<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSalariesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('salaries', function (Blueprint $table) {
			$table->integer('other_deduction_reason')->nullable()->after('tightening_date');
			$table->integer('other_deduction_amount')->nullable()->after('tightening_date');
			$table->integer('other_payment_reason')->nullable()->after('tightening_date');
			$table->integer('other_payment_amount')->nullable()->after('tightening_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('salaries', function (Blueprint $table) {
			//
		});
	}
}
