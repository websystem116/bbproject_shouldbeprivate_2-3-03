<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToChargeProgresssTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charge_progresss', function (Blueprint $table) {
			$table->string('sales_data_created_flg')->nullable(true)->change();
			$table->string('sales_data_created_date')->nullable(true)->change();
			$table->string('charge_data_created_flg')->nullable(true)->change();
			$table->string('charge_data_created_date')->nullable(true)->change();
			$table->string('withdrawal_flg')->nullable(true)->change();
			$table->string('withdrawal_date')->nullable(true)->change();
			$table->string('withdrawal_import_flg')->nullable(true)->change();
			$table->string('withdrawal_import_date')->nullable(true)->change();
			$table->string('monthly_processing_date')->nullable(true)->change();
			$table->string('new_monthly_processing_month')->nullable(true)->change();
			$table->string('sales_month')->nullable(true)->change();  //カラム追加
			$table->string('charge_confirm_flg')->nullable(true)->change();  //カラム追加
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('charge_progresss', function (Blueprint $table) {
			//
		});
	}
}
