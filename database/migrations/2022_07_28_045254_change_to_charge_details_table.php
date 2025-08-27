<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToChargeDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charge_details', function (Blueprint $table) {
			$table->string('student_id')->nullable(true)->change();
			$table->string('charge_id')->nullable(true)->change();

			$table->string('sale_month')->nullable(true)->change();
			$table->string('charge_id')->nullable(true)->change();
			$table->string('charges_date')->nullable(true)->change();
			$table->string('product_id')->nullable(true)->change();
			$table->string('product_name')->nullable(true)->change();
			$table->string('product_price')->nullable(true)->change();
			$table->string('product_price_display')->nullable(true)->change();
			$table->string('price')->nullable(true)->change();
			$table->string('tax')->nullable(true)->change();
			$table->string('subtotal')->nullable(true)->change();
			$table->string('remarks')->nullable(true)->change();
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
			//
		});
	}
}
