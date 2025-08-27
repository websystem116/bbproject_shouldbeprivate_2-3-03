<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->nullable()->comment('支店銀行コード');
            $table->string('bank_id')->nullable()->comment('銀行コード');
            $table->string('name', 15)->nullable()->comment('支店名');
            $table->string('name_kana', 40)->nullable()->comment('支店名（カナ）');
            $table->string('zipcode', 8)->nullable()->comment('未使用');
            $table->string('address', 60)->nullable()->comment('未使用');
            $table->string('tel', 15)->nullable()->comment('未使用');
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
        Schema::dropIfExists('branch_banks');
    }
}
