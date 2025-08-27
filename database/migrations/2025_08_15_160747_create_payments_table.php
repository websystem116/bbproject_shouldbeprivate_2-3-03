<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_id', 40)->comment('生徒テーブルID');
            $table->string('sale_month', 40)->comment('年月');
            $table->string('sale_id', 40)->nullable()->comment('売上テーブルID');
            $table->string('school_building_id', 40)->comment('校舎No');
            $table->string('payment_date', 40)->comment('入金日');
            $table->string('payment_amount', 40)->comment('入金額');
            $table->integer('pay_method')->comment('区分');
            $table->string('summary')->nullable()->comment('摘要');
            $table->string('scrubed_month', 40)->nullable()->comment('消込完了月');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_at', 'student_id', 'payment_amount', 'sale_month'], 'kousin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
