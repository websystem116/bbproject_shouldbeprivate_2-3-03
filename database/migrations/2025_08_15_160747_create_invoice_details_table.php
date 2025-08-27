<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_no', 20)->nullable()->comment('生徒No');
            $table->string('sale_month')->nullable()->comment('年月');
            $table->string('invoice_id')->nullable()->comment('請求書テーブルID');
            $table->string('charges_date')->nullable()->comment('売上日');
            $table->string('product_id')->nullable()->comment('商品ID');
            $table->string('product_name')->nullable()->comment('商品名');
            $table->integer('product_price')->nullable()->comment('価格');
            $table->integer('product_price_display')->nullable()->comment('価格表示(内税・外税)');
            $table->integer('price')->nullable()->comment('金額(割引後)');
            $table->integer('tax')->nullable()->comment('消費税額');
            $table->integer('subtotal')->nullable()->comment('小計');
            $table->string('remarks')->nullable()->comment('備考');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->softDeletes()->comment('削除日');
            $table->timestamps();
            $table->string('sales_number')->nullable()->index()->comment('売上No 売上テーブルと紐付け');
            $table->string('division_name')->nullable()->comment('売上区分名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
}
