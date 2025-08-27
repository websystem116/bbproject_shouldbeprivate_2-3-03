<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subjects', function (Blueprint $table) {
			$table->string('school_classification')->nullable()->after('result_category_id')->comment('学校区分');
			$table->string('university_classification')->nullable()->after('school_classification')->comment('公立区分');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subjects', function (Blueprint $table) {
			//
		});
	}
}
