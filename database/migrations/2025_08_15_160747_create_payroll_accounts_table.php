<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignor_code')->comment('委託者コード');
            $table->integer('bank_id')->nullable();
            $table->integer('branch_bank_id')->nullable();
            $table->integer('account_number')->nullable();
            $table->string('consignor_name', 50)->nullable();
            $table->integer('account_type_id')->nullable();
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
        Schema::dropIfExists('payroll_accounts');
    }
}
