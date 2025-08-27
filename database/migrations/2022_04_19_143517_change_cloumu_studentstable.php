<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCloumuStudentstable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			$table->increments('id', 8)->comment('生徒No')->change();
			$table->string('surname', 40)->nullable()->comment('姓')->change();
			$table->string('name', 40)->nullable()->comment('名')->change();
			$table->string('surname_kana', 40)->nullable()->comment('姓カナ')->change();
			$table->string('name_kana', 40)->nullable()->comment('名カナ')->change();
			$table->dateTime('birthdate')->nullable()->comment('生年月日')->change();
			$table->string('gender', 1)->nullable()->comment('性別')->change();
			$table->string('zip_code', 10)->nullable()->comment('郵便番号')->change();
			$table->string('address1')->nullable()->comment('住所1')->change();
			$table->string('address2')->nullable()->comment('住所2')->change();
			$table->string('address3')->nullable()->comment('住所3')->change();
			$table->string('phone1', 21)->nullable()->comment('電話番号1')->change();
			$table->string('fax', 21)->nullable()->comment('FAX')->change();
			$table->string('phone2', 21)->nullable()->comment('電話番号2')->change();
			$table->string('email', 254)->nullable()->comment('Email')->change();
			$table->string('fax_flg', 1)->nullable()->comment('FAX送信希望フラグ')->change();
			$table->string('school_id', 3)->nullable()->comment('学校No')->change();
			$table->string('grade', 2)->nullable()->comment('現在の学年')->change();

			$table->string('parent_surname', 40)->nullable()->comment('保護者氏名 姓')->change();
			$table->string('parent_name', 40)->nullable()->comment('保護者氏名 名')->change();
			$table->string('parent_surname_kana', 40)->nullable()->comment('保護者氏名 姓カナ')->change();
			$table->string('parent_name_kana', 40)->nullable()->comment('保護者氏名 名カナ')->change();

			$table->string('brothers_name1', 40)->nullable()->comment('兄弟姉妹1 名')->change();
			$table->string('brothers_gender1', 1)->nullable()->comment('兄弟姉妹1 性別')->change();
			$table->string('brothers_grade1', 2)->nullable()->comment('兄弟姉妹1 学年')->change();
			$table->string('brothers_school_no1', 3)->nullable()->comment('兄弟姉妹1 学校No')->change();

			$table->string('brothers_name2', 40)->nullable()->comment('兄弟姉妹2 名')->change();
			$table->string('brothers_gender2', 1)->nullable()->comment('兄弟姉妹2 性別')->change();
			$table->string('brothers_grade2', 2)->nullable()->comment('兄弟姉妹2 学年')->change();
			$table->string('brothers_school_no2', 3)->nullable()->comment('兄弟姉妹2 学校No')->change();

			$table->string('brothers_name3', 40)->nullable()->comment('兄弟姉妹3 名')->change();
			$table->string('brothers_gender3', 1)->nullable()->comment('兄弟姉妹3 性別')->change();
			$table->string('brothers_grade3', 2)->nullable()->comment('兄弟姉妹3 学年')->change();
			$table->string('brothers_school_no3', 3)->nullable()->comment('兄弟姉妹3 学校No')->change();

			$table->string('brothers_flg', 1)->nullable()->comment('兄弟姉妹在塾フラグ')->change();
			$table->string('fatherless_flg', 1)->nullable()->comment('母子家庭フラグ')->change();

			$table->string('bank_id', 4)->nullable()->comment('銀行コード')->change();
			$table->string('branch_code', 4)->nullable()->comment('支店コード')->change();
			$table->string('bank_number', 7)->nullable()->comment('口座番号')->change();
			$table->string('bank_holder', 40)->nullable()->comment('口座名義')->change();
			$table->string('bank_type', 1)->nullable()->comment('口座種別')->change();

			$table->string('discount_id', 3)->nullable()->comment('割引No')->change();
			$table->string('debit_stop_flg', 1)->nullable()->comment('引き落とし停止フラグ')->change();
			$table->dateTime('debit_stop_start_date')->nullable()->comment('引き落とし停止開始日')->change();
			$table->string('school_buildings_id', 3)->nullable()->comment('校舎No')->change();
			$table->string('juku_class', 5)->nullable()->comment('入塾時クラス')->change();

			$table->dateTime('juku_start_date')->nullable()->comment('入塾日')->change();
			$table->dateTime('billing_start_date')->nullable()->comment('請求開始日')->change();
			$table->dateTime('juku_rest_date')->nullable()->comment('休塾日')->change();
			$table->dateTime('juku_return_date')->nullable()->comment('復塾日')->change();
			$table->dateTime('juku_graduation_date')->nullable()->comment('卒塾日')->change();
			$table->dateTime('juku_withdrawal_date')->nullable()->nullable()->comment('退塾日')->change();
			$table->dateTime('high_school_exam_year')->nullable()->comment('高校受験年度')->change();

			$table->string('choice_private_school_id1', 3)->nullable()->comment('志望校私立1 学校No')->change();
			$table->string('choice_private_school_course1', 20)->nullable()->comment('志望校私立1 コース')->change();
			$table->string('choice_private_school_result1', 1)->nullable()->comment('志望校私立1 学校合否')->change();

			$table->string('choice_private_school_id2', 3)->nullable()->comment('志望校私立2 学校No')->change();
			$table->string('choice_private_school_course2', 20)->nullable()->comment('志望校私立2 コース')->change();
			$table->string('choice_private_school_result2', 1)->nullable()->comment('志望校私立2 学校合否')->change();

			$table->string('choice_private_school_id3', 3)->nullable()->comment('志望校私立3 学校No')->change();
			$table->string('choice_private_school_course3', 20)->nullable()->comment('志望校私立3 コース')->change();
			$table->string('choice_private_school_result3', 1)->nullable()->comment('志望校私立3 学校合否')->change();

			$table->string('choice_public_school_id1', 3)->nullable()->comment('志望公立校 学校No')->change();
			$table->string('choice_public_school_course1', 20)->nullable()->comment('志望公立校 コース')->change();
			$table->string('choice_public_school_result1', 1)->nullable()->comment('志望公立校 学校合否')->change();

			$table->string('choice_public_school_id2', 3)->nullable()->comment('志望公立校特色 学校No')->change();
			$table->string('choice_public_school_course2', 20)->nullable()->comment('志望公立校特色 コース')->change();
			$table->string('choice_public_school_result2', 1)->nullable()->comment('志望公立校特色 学校合否')->change();

			$table->dateTime('lessons_year')->nullable()->comment('講習受講年度')->change();
			$table->string('lessons_id')->nullable()->comment('講習受講コード', 2)->change();
			$table->string('lessons_name')->nullable()->comment('講習受講名', 2)->change();

			$table->string('ad_media')->nullable()->comment('広告媒体', 1)->change();
			$table->string('ad_media_note')->nullable()->comment('広告備考', 40)->change();

			$table->string('juku_history_name')->nullable()->comment('通塾歴 塾名', 40)->change();
			$table->string('juku_history_date')->nullable()->comment('通塾歴 期間')->change();

			$table->string('created_by')->nullable()->comment('登録者', 10)->change();
			$table->string('updated_by')->nullable()->comment('登録者', 10)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('students', function (Blueprint $table) {
			//
		});
	}
}
