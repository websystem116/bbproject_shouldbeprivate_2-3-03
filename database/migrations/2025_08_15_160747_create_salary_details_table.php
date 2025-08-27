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
            $table->integer('job_description_id')->comment('業務内容');
            $table->integer('payment_amount')->comment('支給額');
            $table->integer('hourly_wage')->nullable()->comment('時給');
            $table->integer('description_division')->nullable()->comment('業務内容区分　1:業務内容　2：業務内容その他（会議など）');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->timestamps();
            $table->date('attendance_date')->nullable()->comment('出勤日');
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
