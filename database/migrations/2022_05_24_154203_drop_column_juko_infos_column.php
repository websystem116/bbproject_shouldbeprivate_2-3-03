<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnJukoInfosColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('juko_infos', function (Blueprint $table) {


			$table->renameColumn('product_id1', 'product_id');

			$table->dropColumn('product_id2');
			$table->dropColumn('product_id3');
			$table->dropColumn('product_id4');
			$table->dropColumn('product_id5');
			$table->dropColumn('product_id6');
			$table->dropColumn('product_id7');
			$table->dropColumn('product_id8');
			$table->dropColumn('product_id9');
			$table->dropColumn('product_id10');
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
