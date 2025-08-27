<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->comment('パスワード');
            $table->rememberToken();
            $table->timestamps();
            $table->string('last_name')->nullable()->comment('名前(姓)');
            $table->string('first_name')->nullable()->comment('名前(名)');
            $table->string('last_name_kana')->nullable()->comment('名前(姓カナ)');
            $table->string('first_name_kana')->nullable()->comment('名前(名カナ)');
            $table->date('birthday')->nullable()->comment('誕生日');
            $table->string('sex')->nullable()->comment('性別');
            $table->string('post_code')->nullable()->comment('郵便番号');
            $table->string('address1')->nullable()->comment('住所1');
            $table->string('address2')->nullable()->comment('住所２');
            $table->string('address3')->nullable()->comment('住所３');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->integer('school_building')->nullable()->comment('校舎');
            $table->integer('employment_status')->nullable()->comment('職務');
            $table->integer('occupation')->nullable()->comment('職種');
            $table->integer('class_wage')->nullable()->comment('クラス時給');
            $table->integer('personal_wage')->nullable()->comment('個別指導時給');
            $table->integer('pc_wage')->nullable()->comment('パソコンチューター時給');
            $table->integer('office_wage')->nullable()->comment('事務時給');
            $table->integer('creator')->nullable()->comment('作成者');
            $table->integer('updater')->nullable()->comment('更新者');
            $table->date('retirement_date')->nullable()->comment('退社日');
            $table->softDeletes();
            $table->string('user_id')->nullable()->comment('ユーザーID（ログイン時）');
            $table->date('hiredate')->nullable()->comment('入社日');
            $table->integer('description_column')->nullable()->comment('摘要欄');
            $table->integer('deductible_spouse')->nullable()->comment('控除対象配偶者');
            $table->integer('dependents_count')->nullable()->comment('控除対象扶養親族数');
            $table->integer('bank_id')->nullable()->comment('銀行コード');
            $table->string('branch_id', 5)->nullable()->comment('支店コード');
            $table->string('account_type', 20)->nullable()->comment('口座種別');
            $table->string('account_number', 20)->nullable()->comment('口座番号');
            $table->string('recipient_name')->nullable()->comment('受取人');
            $table->string('roles')->nullable()->comment('権限区分');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
