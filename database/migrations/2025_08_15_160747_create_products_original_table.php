<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsOriginalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_original', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number')->comment('編集用No');
            $table->string('name', 40)->nullable()->comment('商品名');
            $table->string('name_short', 10)->nullable()->comment('商品名（略称）');
            $table->string('description', 80)->nullable()->comment('内容');
            $table->integer('price')->nullable()->comment('価格');
            $table->string('tax_category')->nullable()->comment('価格表示:1:内税2:外税');
            $table->string('division_code', 2)->nullable()->comment('売上区分:const.phpのdivision_code参照');
            $table->string('item_no', 2)->nullable()->comment('未使用');
            $table->string('tabulation', 2)->nullable()->comment('集計区分:const.phpのclass_categories参照');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_original');
    }
}
