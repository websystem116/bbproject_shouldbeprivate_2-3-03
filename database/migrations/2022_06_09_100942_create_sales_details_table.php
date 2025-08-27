<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_details', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('student_id', 40)->comment('生徒テーブルID');
			$table->string('sale_month', 40)->comment('年月');
			$table->string('sale_id', 40)->comment('売上テーブルID');
			$table->string('sales_date', 40)->comment('売上日');
			$table->string('product_id', 5)->comment('商品ID');
			$table->string('product_name', 5)->comment('商品名');
			$table->string('product_price', 5)->comment('価格');
			$table->string('product_price_display', 40)->comment('価格表示(内税・外税)');
			$table->string('sales_category', 40)->comment('売上区分');
			$table->string('price', 5)->comment('金額(割引後)');
			$table->string('tax', 5)->comment('消費税額');
			$table->string('subtotal', 5)->comment('小計');
			$table->string('remarks')->comment('備考');
			$table->string('charged_month', 10)->comment('請求完了月');
			$table->string('scrubed_month', 10)->comment('消込完了月');

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
		Schema::dropIfExists('sales_details');
	}
}
