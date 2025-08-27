<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('students', function (Blueprint $table) {
			$table->increments('id', 8)->comment('生徒No');
			$table->string('surname', 40)->comment('姓');
			$table->string('name', 40)->comment('名');
			$table->string('surname_kana', 40)->comment('姓カナ');
			$table->string('name_kana', 40)->comment('名カナ');
			$table->dateTime('birthdate')->comment('生年月日');
			$table->string('gender', 1)->comment('性別');
			$table->string('zip_code', 10)->comment('郵便番号');
			$table->string('address1')->comment('住所1');
			$table->string('address2')->comment('住所2');
			$table->string('address3')->comment('住所3');
			$table->string('phone1', 21)->comment('電話番号1');
			$table->string('fax', 21)->comment('FAX');
			$table->string('phone2', 21)->comment('電話番号2');
			$table->string('email', 254)->comment('Email');
			$table->string('fax_flg', 1)->comment('FAX送信希望フラグ');
			$table->string('school_id', 3)->comment('学校No');
			$table->string('grade', 2)->comment('現在の学年');

			$table->string('parent_surname', 40)->comment('保護者氏名 姓');
			$table->string('parent_name', 40)->comment('保護者氏名 名');
			$table->string('parent_surname_kana', 40)->comment('保護者氏名 姓カナ');
			$table->string('parent_name_kana', 40)->comment('保護者氏名 名カナ');

			$table->string('brothers_name1', 40)->comment('兄弟姉妹1 名');
			$table->string('brothers_gender1', 1)->comment('兄弟姉妹1 性別');
			$table->string('brothers_grade1', 2)->comment('兄弟姉妹1 学年');
			$table->string('brothers_school_no1', 3)->comment('兄弟姉妹1 学校No');

			$table->string('brothers_name2', 40)->comment('兄弟姉妹2 名');
			$table->string('brothers_gender2', 1)->comment('兄弟姉妹2 性別');
			$table->string('brothers_grade2', 2)->comment('兄弟姉妹2 学年');
			$table->string('brothers_school_no2', 3)->comment('兄弟姉妹2 学校No');

			$table->string('brothers_name3', 40)->comment('兄弟姉妹3 名');
			$table->string('brothers_gender3', 1)->comment('兄弟姉妹3 性別');
			$table->string('brothers_grade3', 2)->comment('兄弟姉妹3 学年');
			$table->string('brothers_school_no3', 3)->comment('兄弟姉妹3 学校No');

			$table->string('brothers_flg', 1)->comment('兄弟姉妹在塾フラグ');
			$table->string('fatherless_flg', 1)->comment('母子家庭フラグ');

			$table->string('bank_id', 4)->comment('銀行コード');
			$table->string('branch_code', 4)->comment('支店コード');
			$table->string('bank_number', 7)->comment('口座番号');
			$table->string('bank_holder', 40)->comment('口座名義');
			$table->string('bank_type', 1)->comment('口座種別');

			$table->string('discount_id', 3)->comment('割引No');
			$table->string('debit_stop_flg', 1)->comment('引き落とし停止フラグ');
			$table->dateTime('debit_stop_start_date')->comment('引き落とし停止開始日');
			$table->string('school_buildings_id', 3)->comment('校舎No');
			$table->string('juku_class', 5)->comment('入塾時クラス');

			$table->dateTime('juku_start_date')->comment('入塾日');
			$table->dateTime('billing_start_date')->comment('請求開始日');
			$table->dateTime('juku_rest_date')->comment('休塾日');
			$table->dateTime('juku_return_date')->comment('復塾日');
			$table->dateTime('juku_graduation_date')->comment('卒塾日');
			$table->dateTime('juku_withdrawal_date')->comment('退塾日');
			$table->dateTime('high_school_exam_year')->comment('高校受験年度');

			$table->string('choice_private_school_id1', 3)->comment('志望校私立1 学校No');
			$table->string('choice_private_school_course1', 20)->comment('志望校私立1 コース');
			$table->string('choice_private_school_result1', 1)->comment('志望校私立1 学校合否');

			$table->string('choice_private_school_id2', 3)->comment('志望校私立2 学校No');
			$table->string('choice_private_school_course2', 20)->comment('志望校私立2 コース');
			$table->string('choice_private_school_result2', 1)->comment('志望校私立2 学校合否');

			$table->string('choice_private_school_id3', 3)->comment('志望校私立3 学校No');
			$table->string('choice_private_school_course3', 20)->comment('志望校私立3 コース');
			$table->string('choice_private_school_result3', 1)->comment('志望校私立3 学校合否');

			$table->string('choice_public_school_id1', 3)->comment('志望公立校 学校No');
			$table->string('choice_public_school_course1', 20)->comment('志望公立校 コース');
			$table->string('choice_public_school_result1', 1)->comment('志望公立校 学校合否');

			$table->string('choice_public_school_id2', 3)->comment('志望公立校特色 学校No');
			$table->string('choice_public_school_course2', 20)->comment('志望公立校特色 コース');
			$table->string('choice_public_school_result2', 1)->comment('志望公立校特色 学校合否');

			$table->dateTime('lessons_year')->comment('講習受講年度');
			$table->string('lessons_id')->comment('講習受講コード', 2);
			$table->string('lessons_name')->comment('講習受講名', 2);

			$table->string('ad_media')->comment('広告媒体', 1);
			$table->string('ad_media_note')->comment('広告備考', 40);

			$table->string('juku_history_name')->comment('通塾歴 塾名', 40);
			$table->string('juku_history_date')->comment('通塾歴 期間');

			$table->string('created_by')->comment('登録者', 10);
			$table->string('updated_by')->comment('登録者', 10);


			$table->softDeletes();  // ソフトデリート
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
		Schema::dropIfExists('students');
	}
}
