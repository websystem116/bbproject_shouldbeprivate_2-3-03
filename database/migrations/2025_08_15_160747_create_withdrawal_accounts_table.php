<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignor_code')->nullable()->comment('委託者コード');
            $table->integer('bank_id')->nullable()->comment('銀行コード');
            $table->integer('branch_bank_id')->nullable()->comment('銀行支店コード');
            $table->integer('account_number')->nullable()->comment('口座番号');
            $table->string('consignor_name', 50)->nullable()->comment('委託者名');
            $table->integer('account_type_id')->nullable()->comment('口座種別ID');
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
        Schema::dropIfExists('withdrawal_accounts');
    }
}
