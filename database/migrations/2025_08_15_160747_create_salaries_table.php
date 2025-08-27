<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id', 20)->comment('ユーザーID');
            $table->string('tightening_date', 20)->nullable()->index('tightening_date')->comment('締年月');
            $table->integer('other_payment_amount')->nullable()->comment('その他支払額');
            $table->string('other_payment_reason')->nullable()->comment('その他支払い理由');
            $table->integer('other_deduction_amount')->nullable()->comment('その他控除額');
            $table->string('other_deduction_reason')->nullable()->comment('その他控除理由');
            $table->integer('transportation_expenses')->nullable()->comment('交通費合計');
            $table->integer('salary')->nullable()->comment('給与合計');
            $table->integer('year_end_adjustment')->nullable()->default(0)->comment('年末調整額');
            $table->integer('monthly_completion')->nullable()->comment('月次完了');
            $table->integer('monthly_approval')->nullable()->comment('月次承認');
            $table->integer('salary_approval')->nullable()->comment('給与承認');
            $table->integer('monthly_tightening')->nullable()->comment('月次締め');
            $table->integer('creator')->nullable()->comment('登録者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->timestamp('created_at')->nullable()->comment('作成日');
            $table->timestamp('updated_at')->nullable()->comment('更新日');
            $table->softDeletes()->comment('削除日');
            $table->date('attendance_date')->nullable()->comment('出勤日');
            $table->integer('other_deduction2_amount')->nullable()->default(0)->comment('その他控除額２');
            $table->string('other_deduction2_reason', 300)->nullable()->comment('その他控除理由２');
            $table->integer('other_deduction3_amount')->nullable()->default(0)->comment('その他控除額（源泉徴収額含まない）');
            $table->string('other_deduction3_reason', 300)->nullable()->comment('その他控除理由（源泉徴収額含まない）');
            $table->integer('health_insurance')->nullable()->default(0)->comment('健康保険料');
            $table->integer('welfare_pension')->nullable()->default(0)->comment('厚生年金保険料');
            $table->integer('employment_insurance')->nullable()->default(0)->comment('雇用保険料');
            $table->integer('municipal_tax')->nullable()->default(0)->comment('市町村民税');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
