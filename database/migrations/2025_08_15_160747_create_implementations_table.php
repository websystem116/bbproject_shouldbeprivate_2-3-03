<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImplementationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('implementations', function (Blueprint $table) {
            $table->increments('id')->comment('実施回No(主キー)');
            $table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
            $table->string('implementation_no', 100)->nullable()->comment('カテゴリーごとの実施回No');
            $table->string('implementation_name', 100)->nullable()->comment('実施');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
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
        Schema::dropIfExists('implementations');
    }
}
