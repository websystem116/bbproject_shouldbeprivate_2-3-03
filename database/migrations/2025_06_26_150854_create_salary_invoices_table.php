<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('user_id', 20)->nullable()->comment('ユーザーID');
			$table->string('tightening_date', 20)->nullable()->comment('締年月');
			$table->integer('salary')->nullable()->comment('給与合計');
			$table->integer('monthly_completion')->nullable()->comment('月次完了');
			$table->integer('monthly_approval')->nullable()->comment('月次承認');
			$table->integer('salary_approval')->nullable()->comment('給与承認');
			$table->integer('monthly_tightening')->nullable()->comment('月次締め');
			$table->date('attendance_date')->nullable()->comment('出勤日');
			$table->integer('municipal_tax')->nullable()->comment('市町村民税');
			$table->integer('health_insurance')->nullable()->comment('健康保険料');
			$table->integer('welfare_pension')->nullable()->comment('厚生年金保険料');
			$table->integer('employment_insurance')->nullable()->comment('雇用保険料');

            $table->string('user_name', 20)->nullable()->comment('名前(姓) 名前(名)');
            $table->string('user_name_kana', 20)->nullable()->comment('名前(姓カナ)');
            $table->string('email', 50)->nullable()->comment('メールアドレス');
            $table->string('address1', 20)->nullable()->comment('住所1');
            $table->string('address2', 20)->nullable()->comment('住所２');
            $table->string('address3', 20)->nullable()->comment('住所３');
            $table->string('post_code', 20)->nullable()->comment('郵便番号');
            $table->string('tel', 20)->nullable()->comment('電話番号');
            $table->string('school_building_name', 20)->nullable()->comment('校舎名');
            $table->string('recipient_name', 20)->nullable()->comment('受取人');
            $table->string('roles', 20)->nullable()->comment('権限区分');

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
        Schema::dropIfExists('salary_invoices');
    }
}
