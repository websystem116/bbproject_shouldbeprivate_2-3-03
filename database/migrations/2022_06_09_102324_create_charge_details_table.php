<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charge_details', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('student_id', 40)->comment('生徒テーブルID');
			$table->string('sale_id', 40)->comment('売上テーブルID');

			$table->string('sale_month', 40)->comment('年月');
			$table->string('charge_id', 40)->comment('売上テーブルID');
			$table->string('charges_date', 40)->comment('請求日');
			$table->string('product_id', 5)->comment('商品ID');
			$table->string('product_name', 5)->comment('商品名');
			$table->string('product_price', 5)->comment('価格');
			$table->string('product_price_display', 40)->comment('価格表示(内税・外税)');
			$table->string('price', 5)->comment('金額(割引後)');
			$table->string('tax', 5)->comment('消費税額');
			$table->string('subtotal', 5)->comment('小計');
			$table->string('remarks')->comment('備考');

			$table->integer('creator')->comment('登録者');
			$table->integer('updater')->comment('更新者');

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
		Schema::dropIfExists('charge_details');
	}
}
