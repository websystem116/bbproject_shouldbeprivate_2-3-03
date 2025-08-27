<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeProgresssTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charge_progresss', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('sales_data_created_flg', 40)->comment('当月売上データ作成実行済みフラグ');
			$table->string('sales_data_created_date', 40)->comment('当月売上データ作成実行日');
			$table->string('charge_data_created_flg', 40)->comment('当月請求データ作成実行済みフラグ');
			$table->string('charge_data_created_date', 40)->comment('当月請求データ作成実行日');
			$table->string('withdrawal_flg', 40)->comment('自動引落実行済みフラグ');
			$table->string('withdrawal_date', 40)->comment('自動引落実行日');
			$table->string('withdrawal_import_flg', 40)->comment('引落データ取り込み実行済みフラグ');
			$table->string('withdrawal_import_date', 40)->comment('引落データ取り込み実行日');
			$table->string('monthly_processing_date', 40)->comment('月次処理実行日');
			$table->string('new_monthly_processing_month', 40)->comment('月次処理最新実行月');

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
		Schema::dropIfExists('charge_progresss');
	}
}
