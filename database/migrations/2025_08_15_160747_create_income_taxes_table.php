<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_taxes', function (Blueprint $table) {
            $table->integer('id', true)->comment('所得税計算用');
            $table->integer('or_more')->comment('以上');
            $table->integer('less_than')->comment('未満');
            $table->integer('support0')->comment('扶養０人');
            $table->integer('support1')->comment('扶養１人');
            $table->integer('support2')->comment('扶養２人');
            $table->integer('support3')->comment('扶養３人');
            $table->integer('support4')->comment('扶養４人');
            $table->integer('support5')->comment('扶養５人');
            $table->integer('support6')->comment('扶養６人');
            $table->integer('support7')->comment('扶養７人');
            $table->integer('otsu')->comment('乙');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_taxes');
    }
}
