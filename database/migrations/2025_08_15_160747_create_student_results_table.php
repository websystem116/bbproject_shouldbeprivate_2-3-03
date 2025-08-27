<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_results', function (Blueprint $table) {
            $table->increments('id')->comment('生徒成績No(主キー)');
            $table->string('student_id')->nullable()->comment('生徒No');
            $table->string('student_no', 100)->nullable()->comment('生徒CD');
            $table->string('year', 5)->nullable()->comment('年度');
            $table->string('school_id', 10)->nullable()->comment('学校No 旧システム用
たぶんいらない');
            $table->string('grade', 5)->nullable()->comment('学年');
            $table->string('result_category_id')->nullable()->index('category_id')->comment('成績カテゴリーNo');
            $table->string('implementation_no', 100)->comment('実施回(成績カテゴリーごと)');
            $table->string('subject_no')->nullable()->comment('教科No（成績カテゴリーごと）');
            $table->string('point')->nullable()->comment('点数・偏差値');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
            $table->timestamps();

            $table->index(['student_no', 'grade', 'result_category_id', 'implementation_no', 'subject_no'], 'student_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_results');
    }
}
