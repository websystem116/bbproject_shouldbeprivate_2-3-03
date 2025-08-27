<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id', 20)->comment('ユーザーID');
            $table->string('work_month', 20)->comment('締年月');
            $table->string('salary_id')->nullable()->comment('給与テーブルID');
            $table->string('work_date', 20)->comment('出勤日');
            $table->integer('school_building_id')->comment('校舎No');
            $table->integer('job_description_id')->nullable()->comment('業務内容');
            $table->integer('school_year')->nullable()->comment('学年');
            $table->integer('working_time')->comment('労働時間');
            $table->string('remarks')->nullable()->comment('備考');
            $table->string('superior_approval')->nullable()->comment('上長承認');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('salary_confirmation')->default(false)->comment('給与確認');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_salaries');
    }
}
