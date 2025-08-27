<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_details', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('salary_id', 20)->comment('給与テーブルID');
			$table->integer('job_description')->comment('業務内容');
			$table->integer('payment_amount')->comment('支給額');
			$table->integer('hourly_wage')->comment('時給');

			$table->integer('creator')->comment('登録者');
			$table->integer('updater')->comment('更新者');

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
		Schema::dropIfExists('salary_details');
	}
}
