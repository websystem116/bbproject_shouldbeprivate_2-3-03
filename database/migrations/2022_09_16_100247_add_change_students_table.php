<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeStudentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->string('student_no')->nullable()->after('id')->comment('生徒CD');
			$table->renameColumn('school_classification', 'school_classification1');
			$table->renameColumn('choice_private_school_id', 'choice_private_school_name1');

			// $table->string('choice_private_school_name2')->nullable()->after('choice_private_school_name1')->comment('進学先2');
			// $table->string('school_classification2')->nullable()->after('school_classification1')->comment('生徒CD');

			// $table->string('choice_private_school_name3')->nullable()->after('choice_private_school_name2')->comment('進学先2');
			// $table->string('school_classification3')->nullable()->after('school_classification2')->comment('生徒CD');

			// $table->string('choice_private_school_name4')->nullable()->after('choice_private_school_name3')->comment('進学先2');
			// $table->string('school_classification4')->nullable()->after('school_classification3')->comment('生徒CD');

			// $table->string('choice_private_school_name5')->nullable()->after('choice_private_school_name4')->comment('進学先2');
			// $table->string('school_classification5')->nullable()->after('school_classification4')->comment('生徒CD');
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
