<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnJukoInfosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('juko_infos', function (Blueprint $table) {

			$table->string('student_id', 8)->nullable()->comment('生徒No')->change();
			$table->string('created_by')->nullable()->comment('登録者', 10)->change();
			$table->string('updated_by')->nullable()->comment('登録者', 10)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('juko_infos', function (Blueprint $table) {
			//
		});
	}
}
