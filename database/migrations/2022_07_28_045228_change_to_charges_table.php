<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToChargesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charges', function (Blueprint $table) {
			$table->string('sale_id')->nullable(true)->change();

			$table->string('charge_month')->nullable(true)->change();
			$table->string('carryover')->nullable(true)->change();
			$table->string('month_sum')->nullable(true)->change();
			$table->string('month_tax_sum')->nullable(true)->change();
			$table->string('prepaid')->nullable(true)->change();
			$table->string('sum')->nullable(true)->change();
			$table->string('withdrawal_created_flg')->nullable(true)->change();
			$table->string('withdrawal_confirmed')->nullable(true)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('charges', function (Blueprint $table) {
			//
		});
	}
}
