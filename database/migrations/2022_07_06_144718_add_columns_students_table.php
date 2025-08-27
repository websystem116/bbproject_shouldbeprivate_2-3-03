<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsStudentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->string('school_classification')->nullable()->after('high_school_exam_year')->comment('志望校_学年');
			$table->string('choice_private_school_id', 3)->nullable()->after('school_classification')->comment('志望校');
			$table->string('interview_record')->nullable()->after('juku_history_date')->comment('面談懇談記録');
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
