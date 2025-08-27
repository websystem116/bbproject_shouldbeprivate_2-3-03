<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDescriptionWagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('job_description_wages', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('user_id', 20)->comment('ユーザーID');
			$table->string('job_description_id', 20)->comment('業務内容ID');
			$table->integer('wage')->comment('時給');

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
		Schema::dropIfExists('job_description_wages');
	}
}
