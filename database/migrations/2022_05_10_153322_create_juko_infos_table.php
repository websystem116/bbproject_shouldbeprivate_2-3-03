<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJukoInfosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('juko_infos', function (Blueprint $table) {
			$table->increments('id', 8)->comment('明細No(主キー)');
			$table->string('student_id', 8)->comment('生徒No');
			$table->string('product_id1', 8)->nullable()->comment('商品No1');
			$table->string('product_id2', 8)->nullable()->comment('商品No2');
			$table->string('product_id3', 8)->nullable()->comment('商品No3');
			$table->string('product_id4', 8)->nullable()->comment('商品No4');
			$table->string('product_id5', 8)->nullable()->comment('商品No5');
			$table->string('product_id6', 8)->nullable()->comment('商品No6');
			$table->string('product_id7', 8)->nullable()->comment('商品No7');
			$table->string('product_id8', 8)->nullable()->comment('商品No8');
			$table->string('product_id9', 8)->nullable()->comment('商品No9');
			$table->string('product_id10', 8)->nullable()->comment('商品No10');


			$table->string('created_by')->comment('登録者', 10);
			$table->string('updated_by')->comment('更新者', 10);

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
		Schema::dropIfExists('juko_infos');
	}
}
