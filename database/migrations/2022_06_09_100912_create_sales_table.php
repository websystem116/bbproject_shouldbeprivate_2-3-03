<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('student_id', 40);
			$table->string('sale_month', 40);
			$table->string('school_building_id', 40);
			$table->string('school_id', 40);
			$table->string('school_year', 5);
			$table->string('brothers_flg', 5);
			$table->string('discount_id', 5);
			$table->string('tax', 40);
			$table->string('sales_sum', 40);

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
		Schema::dropIfExists('sales');
	}
}
