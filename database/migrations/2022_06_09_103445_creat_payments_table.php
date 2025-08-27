<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatPaymentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('sales_data_', 40)->comment('生徒テーブルID');
			$table->string('sale_month', 40)->comment('年月');
			$table->string('sale_id', 40)->comment('売上テーブルID');
			$table->string('school_building_id', 40)->comment('校舎No');
			$table->string('payment_date', 40)->comment('入金日');
			$table->string('payment_amount', 40)->comment('入金額');
			$table->string('classification', 5)->comment('区分');
			$table->string('summary', 40)->comment('摘要');
			$table->string('scrubed_month', 40)->comment('消込完了月');

			$table->integer('creator')->comment('登録者');
			$table->integer('updater')->comment('更新者');
			$table->softDeletes();
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
		Schema::dropIfExists('payments');
	}
}
