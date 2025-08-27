<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeStudentsTable2 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->string('school_classification1', 3)->comment('進学校_学年1')->change();
			$table->string('choice_private_school_name1')->nullable()->comment('進学先1')->change();

			$table->string('choice_private_school_name2')->nullable()->after('choice_private_school_name1')->comment('進学先2');
			$table->string('school_classification2', 3)->nullable()->after('school_classification1')->comment('進学校_学年2');

			$table->string('choice_private_school_name3')->nullable()->after('choice_private_school_name2')->comment('進学先3');
			$table->string('school_classification3', 3)->nullable()->after('school_classification2')->comment('進学校_学年3');

			$table->string('choice_private_school_name4')->nullable()->after('choice_private_school_name3')->comment('進学先4');
			$table->string('school_classification4', 3)->nullable()->after('school_classification3')->comment('進学校_学年4');

			$table->string('choice_private_school_name5')->nullable()->after('choice_private_school_name4')->comment('進学先5');
			$table->string('school_classification5', 3)->nullable()->after('school_classification4')->comment('進学校_学年5');
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
