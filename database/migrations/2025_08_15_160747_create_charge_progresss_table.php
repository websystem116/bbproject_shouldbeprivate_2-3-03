<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeProgresssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_progresss', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sales_data_created_flg')->nullable()->comment('当月売上データ作成実行済みフラグ');
            $table->string('sales_data_created_date')->nullable()->comment('当月売上データ作成実行日');
            $table->string('charge_data_created_flg')->nullable()->comment('当月請求データ作成実行済みフラグ');
            $table->string('charge_data_created_date')->nullable()->comment('当月請求データ作成実行日');
            $table->string('withdrawal_nanto_date')->nullable()->comment('自動引落実行済みフラグ');
            $table->string('withdrawal_risona_date')->nullable()->comment('自動引落実行日');
            $table->string('withdrawal_import_nanto_date')->nullable()->comment('引落データ取り込み実行済みフラグ');
            $table->string('withdrawal_import_risona_date')->nullable()->comment('引落データ取り込み実行日');
            $table->dateTime('monthly_processing_date')->nullable()->comment('月次処理実行日');
            $table->string('new_monthly_processing_month')->nullable()->comment('月次処理最新実行月');
            $table->integer('creator')->comment('登録者');
            $table->integer('updater')->comment('更新者');
            $table->softDeletes();
            $table->timestamps();
            $table->string('sales_month')->nullable()->comment('年月');
            $table->string('charge_confirm_flg')->nullable()->comment('請求確定フラグ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_progresss');
    }
}
