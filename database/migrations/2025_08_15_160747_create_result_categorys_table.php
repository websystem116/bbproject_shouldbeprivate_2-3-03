<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultCategorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_categorys', function (Blueprint $table) {
            $table->increments('id')->comment('成績カテゴリーNo(主キー)');
            $table->string('result_category_name', 100)->nullable()->comment('成績カテゴリー名');
            $table->string('average_point_flg', 2)->nullable()->comment('平均点枠表示フラグ');
            $table->string('elementary_school_student_display_flg', 2)->default('0')->comment('小学生表示フラグ');
            $table->string('junior_high_school_student_display_flg', 2)->default('0')->comment('中学生表示フラグ');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
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
        Schema::dropIfExists('result_categorys');
    }
}
