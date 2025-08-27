=<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	class CreateBeforeSalesInfosTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			// Schema::create('before_sales_infos', function (Blueprint $table) {
			// 	$table->increments('id', 8)->comment('明細No(主キー)');
			// 	$table->string('before_student_id', 8)->nullable()->comment('入塾前生徒No');
			// 	$table->string('product_id1', 8)->nullable()->comment('商品No1');
			// 	$table->string('product_id2', 8)->nullable()->comment('商品No2');
			// 	$table->string('product_id3', 8)->nullable()->comment('商品No3');
			// 	$table->string('product_id4', 8)->nullable()->comment('商品No4');
			// 	$table->string('product_id5', 8)->nullable()->comment('商品No5');
			// 	$table->string('product_id6', 8)->nullable()->comment('商品No6');
			// 	$table->string('product_id7', 8)->nullable()->comment('商品No7');
			// 	$table->string('product_id8', 8)->nullable()->comment('商品No8');
			// 	$table->string('product_id9', 8)->nullable()->comment('商品No9');
			// 	$table->string('product_id10', 8)->nullable()->comment('商品No10');
			// 	$table->date('payment_date')->nullable()->comment('入金日');

			// 	$table->string('created_by')->nullable()->comment('登録者', 10);
			// 	$table->string('updated_by')->nullable()->comment('登録者', 10);
			// 	$table->timestamps();
			// });
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::dropIfExists('before_sales_infos');
		}
	}
