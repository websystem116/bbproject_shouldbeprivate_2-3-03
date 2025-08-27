<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDailySalariesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('daily_salaries', function (Blueprint $table) {
			$table->renameColumn('school_building', 'school_building_id');
			$table->renameColumn('job_description', 'job_description_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('daily_salaries', function (Blueprint $table) {
			//
		});
	}
}
