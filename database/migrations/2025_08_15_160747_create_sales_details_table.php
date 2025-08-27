<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_id')->nullable()->comment('生徒テーブルID');
            $table->string('student_no', 20)->nullable()->comment('生徒No');
            $table->string('sale_month')->nullable()->comment('年月');
            $table->string('sale_id')->nullable()->comment('売上テーブルID');
            $table->string('sales_date')->nullable()->comment('売上日');
            $table->string('product_id')->nullable()->comment('商品ID');
            $table->string('product_name')->nullable()->comment('商品名');
            $table->string('product_free', 155)->nullable()->comment('商品名自由入力');
            $table->integer('product_price')->nullable()->comment('価格');
            $table->integer('product_price_display')->nullable()->comment('価格表示(内税・外税)');
            $table->integer('sales_category')->nullable()->comment('売上区分');
            $table->integer('price')->nullable()->comment('金額(割引後)');
            $table->integer('tax')->nullable()->comment('消費税額');
            $table->integer('subtotal')->nullable()->comment('小計');
            $table->string('remarks')->nullable()->comment('備考');
            $table->string('charged_month')->nullable()->comment('請求完了月');
            $table->string('scrubed_month')->nullable()->comment('消込完了月');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes()->comment('削除日');
            $table->timestamp('created_at')->nullable()->comment('作成日');
            $table->timestamp('updated_at')->nullable()->comment('更新日');
            $table->string('sales_number')->nullable()->index('外部キー用')->comment('売上No 売上テーブルと紐付け');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_details');
    }
}
