<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSalesDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sales_details', function (Blueprint $table) {
			$table->string('student_id')->nullable(true)->change();
			$table->string('sale_month')->nullable(true)->change();
			$table->string('sale_id')->nullable(true)->change();
			$table->string('sales_date')->nullable(true)->change();
			$table->string('product_id')->nullable(true)->change();
			$table->string('product_name')->nullable(true)->change();
			$table->string('product_price')->nullable(true)->change();
			$table->string('product_price_display')->nullable(true)->change();
			$table->string('sales_category')->nullable(true)->change();
			$table->string('price')->nullable(true)->change();
			$table->string('tax')->nullable(true)->change();
			$table->string('subtotal')->nullable(true)->change();
			$table->string('remarks')->nullable(true)->change();
			$table->string('charged_month')->nullable(true)->change();
			$table->string('Scrubed_month')->nullable(true)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sales_details', function (Blueprint $table) {
			$table->string('student_id')->nullable(false)->change();
			$table->string('sale_month')->nullable(false)->change();
			$table->string('sale_id')->nullable(false)->change();
			$table->string('sales_date')->nullable(false)->change();
			$table->string('product_id')->nullable(false)->change();
			$table->string('product_name')->nullable(false)->change();
			$table->string('product_price')->nullable(false)->change();
			$table->string('product_price_display')->nullable(false)->change();
			$table->string('sales_category')->nullable(false)->change();
			$table->string('price')->nullable(false)->change();
			$table->string('tax')->nullable(false)->change();
			$table->string('subtotal')->nullable(false)->change();
			$table->string('remarks')->nullable(false)->change();
			$table->string('charged_month')->nullable(false)->change();
			$table->string('Scrubed_month')->nullable(false)->change();
		});
	}
}
