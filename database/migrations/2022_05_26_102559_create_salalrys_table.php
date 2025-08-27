<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalalrysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salarys', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('user_id', 20)->comment('ユーザーID');
			$table->string('tightening_date', 20)->comment('締年月');
			$table->integer('transportation_expenses')->comment('交通費合計');
			$table->integer('salary')->comment('給与合計');
			$table->integer('monthly_completion')->comment('月次完了');
			$table->integer('monthly_approval')->comment('月次承認');
			$table->integer('salary_approval')->comment('給与承認');
			$table->integer('monthly_tightening')->comment('月次締め');
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
		Schema::dropIfExists('salarys');
	}
}
