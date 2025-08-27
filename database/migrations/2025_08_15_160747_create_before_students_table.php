<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeforeStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('before_students', function (Blueprint $table) {
            $table->increments('id')->comment('管理No');
            $table->string('before_student_no')->nullable()->comment('入塾前生徒No');
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
            $table->string('phone2', 21)->nullable()->comment('電話番号2');
            $table->string('email', 254)->nullable()->comment('Email');
            $table->string('fax', 21)->nullable()->comment('FAX');
            $table->string('school_id', 10)->nullable()->comment('学校No');
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
            $table->string('school_building_id', 3)->nullable()->comment('校舎No');
            $table->date('contact_tel_date')->nullable()->comment('問い合わせ日(電話)');
            $table->date('description_juku_date')->nullable()->comment('入塾説明');
            $table->date('coming_juku_date')->nullable()->comment('問い合わせ(来塾)');
            $table->date('juku_test_date')->nullable()->comment('入塾テスト');
            $table->date('document_request_date')->nullable()->comment('問い合わせ(資料請求)');
            $table->date('special_experience_date')->nullable()->comment('特別体験');
            $table->string('sign_up_juku_flg', 1)->nullable()->comment('入塾フラグ');
            $table->string('summer_year', 4)->nullable()->comment('夏期講習');
            $table->string('winter_year', 4)->nullable()->comment('冬期講習');
            $table->string('spring_year', 4)->nullable()->comment('春期講習');
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
        Schema::dropIfExists('before_students');
    }
}
