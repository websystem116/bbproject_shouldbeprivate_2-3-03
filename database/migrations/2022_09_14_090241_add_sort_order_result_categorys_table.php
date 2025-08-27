<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortOrderResultCategorysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('result_categorys', function (Blueprint $table) {
			$table->string('sort_order')->nullable()->after('trial_exam_flg')->comment('並び順');
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
