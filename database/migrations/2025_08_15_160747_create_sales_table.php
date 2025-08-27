<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('売上テーブル');
            $table->string('student_id')->nullable()->comment('生徒id');
            $table->string('student_no', 20)->nullable()->comment('生徒No');
            $table->string('sale_month')->nullable()->index('sale_month')->comment('売上年月');
            $table->string('school_building_id')->nullable()->comment('校舎id');
            $table->string('school_id')->nullable()->comment('学校id');
            $table->string('school_year')->nullable()->comment('学年');
            $table->string('brothers_flg')->nullable()->comment('兄弟フラグ');
            $table->string('discount_id')->nullable()->comment('割引id');
            $table->integer('tax')->nullable()->comment('消費税');
            $table->integer('sales_sum')->nullable()->comment('売上合計');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes()->comment('削除日');
            $table->timestamp('created_at')->nullable()->comment('作成日');
            $table->timestamp('updated_at')->nullable()->comment('更新日');
            $table->string('sales_number')->nullable()->comment('売上No 売上明細と紐付け');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
