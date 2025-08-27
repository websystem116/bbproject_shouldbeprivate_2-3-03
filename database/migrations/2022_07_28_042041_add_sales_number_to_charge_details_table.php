<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesNumberToChargeDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charge_details', function (Blueprint $table) {
			$table->string('sales_number', 40)->comment("売上No 売上テーブルと紐付け");  //カラム追加
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('charge_details', function (Blueprint $table) {
			$table->dropColumn('sales_number');  //カラムの削除
		});
	}
}
