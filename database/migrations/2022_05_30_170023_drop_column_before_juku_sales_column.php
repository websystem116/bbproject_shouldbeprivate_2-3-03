<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnBeforeJukuSalesColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('before_juku_sales', function (Blueprint $table) {

			$table->renameColumn('price_after_discount1', 'price_after_discount');
			$table->renameColumn('note1', 'note');


			$table->dropColumn('sales_date2');
			$table->dropColumn('payment_date2');
			$table->dropColumn('product_id2');
			$table->dropColumn('price_after_discount2');
			$table->dropColumn('note2');

			$table->dropColumn('sales_date3');
			$table->dropColumn('payment_date3');
			$table->dropColumn('product_id3');
			$table->dropColumn('price_after_discount3');
			$table->dropColumn('note3');

			$table->dropColumn('sales_date4');
			$table->dropColumn('payment_date4');
			$table->dropColumn('product_id4');
			$table->dropColumn('price_after_discount4');
			$table->dropColumn('note4');

			$table->dropColumn('sales_date5');
			$table->dropColumn('payment_date5');
			$table->dropColumn('product_id5');
			$table->dropColumn('price_after_discount5');
			$table->dropColumn('note5');

			$table->dropColumn('sales_date6');
			$table->dropColumn('payment_date6');
			$table->dropColumn('product_id6');
			$table->dropColumn('price_after_discount6');
			$table->dropColumn('note6');

			$table->dropColumn('sales_date7');
			$table->dropColumn('payment_date7');
			$table->dropColumn('product_id7');
			$table->dropColumn('price_after_discount7');
			$table->dropColumn('note7');

			$table->dropColumn('sales_date8');
			$table->dropColumn('payment_date8');
			$table->dropColumn('product_id8');
			$table->dropColumn('price_after_discount8');
			$table->dropColumn('note8');

			$table->dropColumn('sales_date9');
			$table->dropColumn('payment_date9');
			$table->dropColumn('product_id9');
			$table->dropColumn('price_after_discount9');
			$table->dropColumn('note9');

			$table->dropColumn('sales_date10');
			$table->dropColumn('payment_date10');
			$table->dropColumn('product_id10');
			$table->dropColumn('price_after_discount10');
			$table->dropColumn('note10');

			$table->dropColumn('sales_date11');
			$table->dropColumn('payment_date11');
			$table->dropColumn('product_id11');
			$table->dropColumn('price_after_discount11');
			$table->dropColumn('note11');

			$table->dropColumn('sales_date12');
			$table->dropColumn('payment_date12');
			$table->dropColumn('product_id12');
			$table->dropColumn('price_after_discount12');
			$table->dropColumn('note12');

			$table->dropColumn('sales_date13');
			$table->dropColumn('payment_date13');
			$table->dropColumn('product_id13');
			$table->dropColumn('price_after_discount13');
			$table->dropColumn('note13');

			$table->dropColumn('sales_date14');
			$table->dropColumn('payment_date14');
			$table->dropColumn('product_id14');
			$table->dropColumn('price_after_discount14');
			$table->dropColumn('note14');

			$table->dropColumn('sales_date15');
			$table->dropColumn('payment_date15');
			$table->dropColumn('product_id15');
			$table->dropColumn('price_after_discount15');
			$table->dropColumn('note15');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('before_juku_sales', function (Blueprint $table) {
			//
		});
	}
}
