<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultCategorysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('result_categorys', function (Blueprint $table) {
			$table->string('average_point_flg')->nullable()->after('university_classification')->comment('平均点枠表示フラグ');
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
