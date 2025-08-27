<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeforeJukuSalesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('before_juku_sales', function (Blueprint $table) {
			$table->increments('id', 8)->comment('明細No(主キー)');
			$table->string('before_student_id', 8)->nullable()->comment('入塾前生徒No');

			$table->date('sales_date1')->nullable()->comment('売上年月1');
			$table->date('payment_date1')->nullable()->comment('入金日1');
			$table->string('product_id1', 8)->nullable()->comment('商品No1');
			$table->string('price_after_discount1', 8)->nullable()->comment('割引後金額(円)1');
			$table->string('note1')->nullable()->comment('備考1', 100);

			$table->date('sales_date2')->nullable()->comment('売上年月2');
			$table->date('payment_date2')->nullable()->comment('入金日2');
			$table->string('product_id2', 8)->nullable()->comment('商品No2');
			$table->string('price_after_discount2', 8)->nullable()->comment('割引後金額(円)2');
			$table->string('note2')->nullable()->comment('備考2', 100);

			$table->date('sales_date3')->nullable()->comment('売上年月3');
			$table->date('payment_date3')->nullable()->comment('入金日3');
			$table->string('product_id3', 8)->nullable()->comment('商品No3');
			$table->string('price_after_discount3', 8)->nullable()->comment('割引後金額(円)3');
			$table->string('note3')->nullable()->comment('備考3', 100);

			$table->date('sales_date4')->nullable()->comment('売上年月4');
			$table->date('payment_date4')->nullable()->comment('入金日4');
			$table->string('product_id4', 8)->nullable()->comment('商品No4');
			$table->string('price_after_discount4', 8)->nullable()->comment('割引後金額(円)4');
			$table->string('note4')->nullable()->comment('備考4', 100);

			$table->date('sales_date5')->nullable()->comment('売上年月5');
			$table->date('payment_date5')->nullable()->comment('入金日5');
			$table->string('product_id5', 8)->nullable()->comment('商品No5');
			$table->string('price_after_discount5', 8)->nullable()->comment('割引後金額(円)5');
			$table->string('note5')->nullable()->comment('備考5', 100);

			$table->date('sales_date6')->nullable()->comment('売上年月6');
			$table->date('payment_date6')->nullable()->comment('入金日6');
			$table->string('product_id6', 8)->nullable()->comment('商品No6');
			$table->string('price_after_discount6', 8)->nullable()->comment('割引後金額(円)6');
			$table->string('note6')->nullable()->comment('備考6', 100);

			$table->date('sales_date7')->nullable()->comment('売上年月7');
			$table->date('payment_date7')->nullable()->comment('入金日7');
			$table->string('product_id7', 8)->nullable()->comment('商品No7');
			$table->string('price_after_discount7', 8)->nullable()->comment('割引後金額(円)7');
			$table->string('note7')->nullable()->comment('備考7', 100);

			$table->date('sales_date8')->nullable()->comment('売上年月8');
			$table->date('payment_date8')->nullable()->comment('入金日8');
			$table->string('product_id8', 8)->nullable()->comment('商品No8');
			$table->string('price_after_discount8', 8)->nullable()->comment('割引後金額(円)8');
			$table->string('note8')->nullable()->comment('備考8', 100);

			$table->date('sales_date9')->nullable()->comment('売上年月9');
			$table->date('payment_date9')->nullable()->comment('入金日9');
			$table->string('product_id9', 8)->nullable()->comment('商品No9');
			$table->string('price_after_discount9', 8)->nullable()->comment('割引後金額(円)9');
			$table->string('note9')->nullable()->comment('備考9', 100);

			$table->date('sales_date10')->nullable()->comment('売上年月10');
			$table->date('payment_date10')->nullable()->comment('入金日10');
			$table->string('product_id10', 8)->nullable()->comment('商品No10');
			$table->string('price_after_discount10', 8)->nullable()->comment('割引後金額(円)10');
			$table->string('note10')->nullable()->comment('備考10', 100);

			$table->date('sales_date11')->nullable()->comment('売上年月11');
			$table->date('payment_date11')->nullable()->comment('入金日11');
			$table->string('product_id11', 8)->nullable()->comment('商品No11');
			$table->string('price_after_discount11', 8)->nullable()->comment('割引後金額(円)11');
			$table->string('note11')->nullable()->comment('備考11', 100);

			$table->date('sales_date12')->nullable()->comment('売上年月12');
			$table->date('payment_date12')->nullable()->comment('入金日12');
			$table->string('product_id12', 8)->nullable()->comment('商品No12');
			$table->string('price_after_discount12', 8)->nullable()->comment('割引後金額(円)12');
			$table->string('note12')->nullable()->comment('備考12', 100);

			$table->date('sales_date13')->nullable()->comment('売上年月13');
			$table->date('payment_date13')->nullable()->comment('入金日13');
			$table->string('product_id13', 8)->nullable()->comment('商品No13');
			$table->string('price_after_discount13', 8)->nullable()->comment('割引後金額(円)13');
			$table->string('note13')->nullable()->comment('備考13', 100);

			$table->date('sales_date14')->nullable()->comment('売上年月14');
			$table->date('payment_date14')->nullable()->comment('入金日14');
			$table->string('product_id14', 8)->nullable()->comment('商品No14');
			$table->string('price_after_discount14', 8)->nullable()->comment('割引後金額(円)14');
			$table->string('note14')->nullable()->comment('備考14', 100);

			$table->date('sales_date15')->nullable()->comment('売上年月15');
			$table->date('payment_date15')->nullable()->comment('入金日15');
			$table->string('product_id15', 8)->nullable()->comment('商品No15');
			$table->string('price_after_discount15', 8)->nullable()->comment('割引後金額(円)15');
			$table->string('note15')->nullable()->comment('備考15', 100);

			$table->string('created_by')->nullable()->comment('登録者', 10);
			$table->string('updated_by')->nullable()->comment('登録者', 10);
			$table->softDeletes();  // ソフトデリート
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
		Schema::dropIfExists('before_juku_sales');
	}
}
