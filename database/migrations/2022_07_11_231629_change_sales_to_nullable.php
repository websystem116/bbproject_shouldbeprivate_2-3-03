<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSalesToNullable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sales', function (Blueprint $table) {
			$table->string('student_id')->nullable(true)->change();
			$table->string('sale_month')->nullable(true)->change();
			$table->string('school_building_id')->nullable(true)->change();
			$table->string('school_id')->nullable(true)->change();
			$table->string('school_year')->nullable(true)->change();
			$table->string('brothers_flg')->nullable(true)->change();
			$table->string('discount_id')->nullable(true)->change();
			$table->string('tax')->nullable(true)->change();
			$table->string('sales_sum')->nullable(true)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sales', function (Blueprint $table) {
			//
		});
	}
}
