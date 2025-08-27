<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherJobDescriptionWagesDeleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_job_description_wages_delete', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('その他業務実績時給テーブル
');
            $table->string('user_id', 20)->comment('ユーザーID');
            $table->string('other_job_description_id', 20)->comment('業務内容ID');
            $table->string('wage')->nullable()->comment('時給');
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
        Schema::dropIfExists('other_job_description_wages_delete');
    }
}
