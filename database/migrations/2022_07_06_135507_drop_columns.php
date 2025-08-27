<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumns extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->dropColumn('choice_private_school_id1');
			$table->dropColumn('choice_private_school_course1');
			$table->dropColumn('choice_private_school_result1');

			$table->dropColumn('choice_private_school_id2');
			$table->dropColumn('choice_private_school_course2');
			$table->dropColumn('choice_private_school_result2');

			$table->dropColumn('choice_private_school_id3');
			$table->dropColumn('choice_private_school_course3');
			$table->dropColumn('choice_private_school_result3');

			$table->dropColumn('choice_public_school_id1');
			$table->dropColumn('choice_public_school_course1');
			$table->dropColumn('choice_public_school_result1');

			$table->dropColumn('choice_public_school_id2');
			$table->dropColumn('choice_public_school_course2');
			$table->dropColumn('choice_public_school_result2');

			$table->dropColumn('lessons_name');
			$table->dropColumn('ad_media');
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
