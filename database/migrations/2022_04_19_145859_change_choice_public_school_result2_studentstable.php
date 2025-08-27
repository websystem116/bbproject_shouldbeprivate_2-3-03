<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeChoicePublicSchoolResult2Studentstable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->string('choice_public_school_result2', 1)->nullable()->comment('志望公立校特色 学校合否')->change();
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
