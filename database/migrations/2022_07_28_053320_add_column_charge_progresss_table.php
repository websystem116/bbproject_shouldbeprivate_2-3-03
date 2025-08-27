<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnChargeProgresssTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charge_progresss', function (Blueprint $table) {
			$table->string('sales_month', 20)->comment("年月");  //カラム追加
			$table->string('charge_confirm_flg', 20)->comment("請求確定フラグ");  //カラム追加
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
