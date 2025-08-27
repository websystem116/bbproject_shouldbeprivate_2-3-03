<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1ResultCategorysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('result_categorys', function (Blueprint $table) {
			$table->string('junior_high_school_grade', 3)->nullable()->after('average_point_flg')->comment('中学学年');
			$table->string('trial_exam_flg', 3)->nullable()->after('junior_high_school_grade')->comment('模試/シート2枚目');
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
