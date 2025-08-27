<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subjects', function (Blueprint $table) {
			$table->increments('id', 8)->comment('教科No(主キー)');
			$table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
			$table->string('subject_name')->nullable()->comment('教科名');
			$table->string('created_by')->nullable()->comment('登録者', 10);
			$table->string('updated_by')->nullable()->comment('登録者', 10);
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
		Schema::dropIfExists('subjects');
	}
}
