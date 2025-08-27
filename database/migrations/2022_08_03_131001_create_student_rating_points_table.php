<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRatingPointsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_rating_points', function (Blueprint $table) {
			$table->increments('id', 8)->comment('評定No(主キー)');
			$table->string('student_id')->nullable()->comment('生徒No');
			$table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
			$table->string('examination_id')->nullable()->comment('試験No');
			$table->string('subject_id')->nullable()->comment('教科No');
			$table->string('rating_point')->nullable()->comment('点数・偏差値');
			$table->string('created_by')->nullable()->comment('登録者', 10);
			$table->string('updated_by')->nullable()->comment('登録者', 10);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('student_rating_points');
	}
}
