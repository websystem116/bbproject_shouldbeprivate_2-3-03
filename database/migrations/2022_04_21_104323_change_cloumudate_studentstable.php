<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCloumudateStudentstable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->date('birthdate')->nullable()->comment('生年月日')->change();
			$table->date('debit_stop_start_date')->nullable()->comment('引き落とし停止開始日')->change();
			$table->date('juku_start_date')->nullable()->comment('入塾日')->change();
			$table->date('billing_start_date')->nullable()->comment('請求開始日')->change();
			$table->date('juku_rest_date')->nullable()->comment('休塾日')->change();
			$table->date('juku_return_date')->nullable()->comment('復塾日')->change();
			$table->date('juku_graduation_date')->nullable()->comment('卒塾日')->change();
			$table->date('juku_withdrawal_date')->nullable()->nullable()->comment('退塾日')->change();
			$table->date('high_school_exam_year')->nullable()->comment('高校受験年度')->change();
			$table->string('lessons_year')->nullable()->comment('講習受講年度')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('students', function (Blueprint $table) {
			//
		});
	}
}
