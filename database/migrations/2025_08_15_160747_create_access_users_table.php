<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_users', function (Blueprint $table) {
            $table->increments('id')->comment('生徒No');
            $table->string('student_no')->nullable()->comment('生徒CD');
            $table->string('surname', 40)->nullable()->comment('姓');
            $table->string('name', 40)->nullable()->comment('名');
            $table->string('surname_kana', 40)->nullable()->comment('姓カナ');
            $table->string('name_kana', 40)->nullable()->comment('名カナ');
            $table->string('birthdate')->nullable()->comment('生年月日');
            $table->string('gender', 1)->nullable()->comment('性別');
            $table->string('zip_code', 10)->nullable()->comment('郵便番号');
            $table->string('address1')->nullable()->comment('住所1');
            $table->string('address2')->nullable()->comment('住所2');
            $table->string('address3')->nullable()->comment('住所3');
            $table->string('phone1', 21)->nullable()->comment('電話番号1');
            $table->string('fax', 21)->nullable()->comment('FAX');
            $table->string('phone2', 21)->nullable()->comment('電話番号2');
            $table->string('email', 254)->nullable()->comment('Email');
            $table->string('fax_flg', 1)->nullable()->comment('FAX送信希望フラグ');
            $table->string('school_id', 100)->nullable()->comment('学校No');
            $table->string('grade', 2)->nullable()->comment('現在の学年');
            $table->string('parent_surname', 40)->nullable()->comment('保護者氏名 姓');
            $table->string('parent_name', 40)->nullable()->comment('保護者氏名 名');
            $table->string('parent_surname_kana', 40)->nullable()->comment('保護者氏名 姓カナ');
            $table->string('parent_name_kana', 40)->nullable()->comment('保護者氏名 名カナ');
            $table->string('brothers_name1', 40)->nullable()->comment('兄弟姉妹1 名');
            $table->string('brothers_gender1', 1)->nullable()->comment('兄弟姉妹1 性別');
            $table->string('brothers_grade1', 2)->nullable()->comment('兄弟姉妹1 学年');
            $table->string('brothers_school_no1', 3)->nullable()->comment('兄弟姉妹1 学校No');
            $table->string('brothers_name2', 40)->nullable()->comment('兄弟姉妹2 名');
            $table->string('brothers_gender2', 1)->nullable()->comment('兄弟姉妹2 性別');
            $table->string('brothers_grade2', 2)->nullable()->comment('兄弟姉妹2 学年');
            $table->string('brothers_school_no2', 3)->nullable()->comment('兄弟姉妹2 学校No');
            $table->string('brothers_name3', 40)->nullable()->comment('兄弟姉妹3 名');
            $table->string('brothers_gender3', 1)->nullable()->comment('兄弟姉妹3 性別');
            $table->string('brothers_grade3', 2)->nullable()->comment('兄弟姉妹3 学年');
            $table->string('brothers_school_no3', 3)->nullable()->comment('兄弟姉妹3 学校No');
            $table->string('brothers_flg', 1)->nullable()->comment('兄弟姉妹在塾フラグ');
            $table->string('fatherless_flg', 1)->nullable()->comment('母子家庭フラグ');
            $table->string('bank_id', 4)->nullable()->comment('銀行コード');
            $table->string('branch_code', 4)->nullable()->comment('支店コード');
            $table->string('bank_number', 7)->nullable()->comment('口座番号');
            $table->string('bank_holder', 40)->nullable()->comment('口座名義');
            $table->string('bank_type', 1)->nullable()->comment('口座種別');
            $table->string('payment_methods')->nullable()->comment('支払い方法');
            $table->string('discount_id', 3)->nullable()->comment('割引No');
            $table->string('debit_stop_flg', 1)->nullable()->comment('引き落とし停止フラグ');
            $table->date('debit_stop_start_date')->nullable()->comment('引き落とし停止開始日');
            $table->string('school_building_id', 3)->nullable()->comment('校舎No');
            $table->string('juku_class', 5)->nullable()->comment('入塾時クラス');
            $table->date('juku_start_date')->nullable()->comment('入塾日');
            $table->date('billing_start_date')->nullable()->comment('請求開始日');
            $table->date('juku_rest_date')->nullable()->comment('休塾日');
            $table->date('juku_return_date')->nullable()->comment('復塾日');
            $table->date('juku_graduation_date')->nullable()->comment('卒塾日');
            $table->date('juku_withdrawal_date')->nullable()->comment('退塾日');
            $table->string('high_school_exam_year', 4)->nullable()->comment('高校受験年度');
            $table->string('school_classification1', 3)->nullable()->comment('進学校_学年1');
            $table->string('school_classification2', 3)->nullable()->comment('進学校_学年2');
            $table->string('school_classification3', 3)->nullable()->comment('進学校_学年3');
            $table->string('school_classification4', 3)->nullable()->comment('進学校_学年4');
            $table->string('school_classification5', 3)->nullable()->comment('進学校_学年5');
            $table->string('choice_private_school_name1')->nullable()->comment('進学先1');
            $table->string('choice_private_school_name2')->nullable()->comment('進学先2');
            $table->string('choice_private_school_name3')->nullable()->comment('進学先3');
            $table->string('choice_private_school_name4')->nullable()->comment('進学先4');
            $table->string('choice_private_school_name5')->nullable()->comment('進学先5');
            $table->string('lessons_year')->nullable()->comment('講習受講年度');
            $table->string('lessons_id')->nullable()->comment('講習受講コード');
            $table->string('ad_media_note')->nullable()->comment('広告備考');
            $table->string('juku_history_name')->nullable()->comment('通塾歴 塾名');
            $table->string('juku_history_date')->nullable()->comment('通塾歴 期間');
            $table->string('interview_record')->nullable()->comment('面談懇談記録');
            $table->string('temporary_flg', 2)->nullable()->default('0')->comment('入塾前から登録が来た場合に1になる
生徒情報編集から1度でも編集されたら0になるフラグ');
            $table->string('comment')->nullable()->comment('コメント');
            $table->string('note')->nullable();
            $table->string('email_access')->nullable();
            $table->string('email_access2')->nullable();
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
        Schema::dropIfExists('access_users');
    }
}
