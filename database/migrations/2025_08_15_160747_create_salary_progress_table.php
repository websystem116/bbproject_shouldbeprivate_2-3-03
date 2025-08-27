<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('new_monthly_processing_month')->nullable()->comment('非常勤管理　締め処理実行月');
            $table->integer('creator')->comment('登録者');
            $table->integer('updater')->comment('更新者');
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
        Schema::dropIfExists('salary_progress');
    }
}
