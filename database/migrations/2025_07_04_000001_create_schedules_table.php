<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->comment('予定タイトル');
            $table->text('content')->nullable()->comment('予定内容');
            $table->date('schedule_date')->comment('予定日');
            $table->time('start_time')->nullable()->comment('開始時間');
            $table->time('end_time')->nullable()->comment('終了時間');
            $table->integer('school_building_id')->unsigned()->comment('校舎ID');
            $table->integer('created_by')->unsigned()->comment('作成者ID');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('承認状況');
            $table->integer('approved_by')->unsigned()->nullable()->comment('承認者ID');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->text('approval_note')->nullable()->comment('承認メモ');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['schedule_date', 'school_building_id']);
            $table->index('status');
            $table->index('school_building_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
