<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJukoInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('juko_infos', function (Blueprint $table) {
            $table->increments('id')->comment('明細No(主キー)');
            $table->string('student_id', 8)->nullable()->comment('生徒No');
            $table->string('student_no')->nullable()->comment('生徒CD');
            $table->string('product_id', 8)->nullable()->comment('商品No1');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
            $table->softDeletes();
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
        Schema::dropIfExists('juko_infos');
    }
}
