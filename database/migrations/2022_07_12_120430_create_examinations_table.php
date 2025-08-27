<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExaminationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('examinations', function (Blueprint $table) {
			$table->increments('id', 8)->comment('試験No(主キー)');
			$table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
			$table->string('school_classification')->nullable()->comment('学校区分');
			$table->string('university_classification')->nullable()->comment('公立区分');
			$table->string('examination_name')->nullable()->comment('試験名');
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
		Schema::dropIfExists('examinations');
	}
}
