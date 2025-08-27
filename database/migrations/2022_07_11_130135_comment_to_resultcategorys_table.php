<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommentToResultcategorysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('result_categorys', function (Blueprint $table) {
			$table->string('result_category_name')->nullable()->comment('成績カテゴリー名')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('result_categorys', function (Blueprint $table) {
			//
		});
	}
}
