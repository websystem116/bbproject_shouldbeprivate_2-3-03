<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_invoice_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('salary_invoice_id', 20)->nullable()->comment('給与テーブルID');
            $table->string('job_description_name', 20)->nullable()->comment('業務内容（名称）');
            $table->integer('payment_amount')->nullable()->comment('支給額');
            $table->integer('hourly_wage')->nullable()->comment('時給');
            $table->date('attendance_date')->nullable()->comment('出勤日');
            $table->integer('municipal_tax')->nullable()->comment('市町村民税');
            $table->integer('deduction')->nullable()->comment('控除');
            $table->integer('salary_sabtotal')->nullable()->comment('給与小計');
            $table->integer('income_tax_cost')->nullable()->comment('所得税費用');
            $table->string('division_name', 30)->nullable();
            $table->integer('transportation_expenses')->nullable()->comment('交通費合計');
            $table->integer('other_payment_amount')->nullable()->comment('その他支払額');
            $table->integer('other_deduction_amount')->nullable()->comment('その他控除額');
            $table->integer('year_end_adjustment')->nullable();
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
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
        Schema::dropIfExists('salary_invoice_details');
    }
}
