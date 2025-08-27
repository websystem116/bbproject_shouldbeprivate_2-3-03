<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySalalrysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('daily_salarys', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('user_id', 20)->comment('ユーザーID');
			$table->string('work_month', 20)->comment('締年月');
			$table->string('salary_id', 20)->comment('給与テーブルID');
			$table->string('work_date', 20)->comment('出勤日');
			$table->integer('school_building')->comment('校舎No');
			$table->integer('job_description')->comment('業務内容');
			$table->integer('school_year')->comment('学年');
			$table->integer('working_time')->comment('労働時間');
			$table->string('remarks')->comment('備考');
			$table->integer('superior_approval')->comment('上長承認');
			$table->integer('creator')->comment('登録者');
			$table->integer('updater')->comment('更新者');


			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('daily_salarys');
	}
}
