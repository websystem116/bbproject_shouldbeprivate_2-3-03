<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultCategorysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('result_categorys', function (Blueprint $table) {
			$table->increments('id', 8)->comment('成績カテゴリーNo(主キー)');
			$table->string('school_classification', 3)->comment('学校区分'); //高校、中学、小学
			$table->string('university_classification', 3)->comment('公立区分'); //国立、私立、公立区分

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
		Schema::dropIfExists('result_categorys');
	}
}
