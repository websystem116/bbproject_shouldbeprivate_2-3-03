<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charges', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('student_id', 40)->comment('生徒テーブルID');
			$table->string('sale_id', 40)->comment('売上テーブルID');

			$table->string('charge_month', 40)->comment('年月');
			$table->string('carryover', 40)->comment('前月繰越金');
			$table->string('month_sum', 5)->comment('当月明細合計');
			$table->string('month_tax_sum', 5)->comment('当月消費税合計');
			$table->string('prepaid', 5)->comment('事前入金');
			$table->string('sum', 40)->comment('合計請求額');
			$table->string('withdrawal_created_flg', 40)->comment('自動引落データ作成済みフラグ');
			$table->string('withdrawal_confirmed', 5)->comment('引落確認');

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
		Schema::dropIfExists('charges');
	}
}
