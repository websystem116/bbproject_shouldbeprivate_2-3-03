<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_no', 40)->comment('生徒テーブルID');
            $table->string('sale_id')->nullable()->comment('売上テーブルID');
            $table->string('charge_month')->nullable()->comment('年月');
            $table->string('carryover')->nullable()->comment('前月繰越金');
            $table->string('month_sum')->nullable()->comment('当月明細合計');
            $table->string('month_tax_sum')->nullable()->comment('当月消費税合計');
            $table->string('prepaid')->nullable()->comment('事前入金');
            $table->string('sum')->nullable()->comment('合計請求額');
            $table->string('withdrawal_created_flg')->nullable()->comment('自動引落データ作成済みフラグ');
            $table->boolean('convenience_store_flg')->nullable()->comment('コンビニ振込フラグ');
            $table->boolean('transferred_flg')->default(false)->comment('移行済みフラグ');
            $table->string('withdrawal_confirmed')->nullable()->comment('引落確認');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes();
            $table->timestamps();
            $table->string('sales_number', 40)->comment('売上No 売上テーブルと紐付け');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
