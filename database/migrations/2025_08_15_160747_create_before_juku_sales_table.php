<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeforeJukuSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('before_juku_sales', function (Blueprint $table) {
            $table->increments('id')->comment('明細No(主キー)');
            $table->string('before_student_no', 100)->nullable()->comment('生徒No');
            $table->string('school_building_id', 20)->nullable()->comment('校舎id');
            $table->string('sales_date', 20)->nullable()->comment('売上年月1');
            $table->date('payment_date')->nullable()->comment('入金日1');
            $table->string('product_id', 8)->nullable()->comment('商品No1');
            $table->string('price_after_discount', 8)->nullable()->comment('割引後金額(円)1');
            $table->string('tax', 100)->nullable()->comment('消費税');
            $table->string('subtotal', 100)->nullable()->comment('小計');
            $table->string('note')->nullable()->comment('備考1');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('before_juku_sales');
    }
}
