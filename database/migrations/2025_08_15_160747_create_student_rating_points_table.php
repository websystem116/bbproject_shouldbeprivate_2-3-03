<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRatingPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_rating_points', function (Blueprint $table) {
            $table->increments('id')->comment('評定No(主キー)');
            $table->string('student_id')->nullable()->comment('生徒No');
            $table->string('student_no', 100)->nullable()->comment('生徒No');
            $table->string('grade', 5)->nullable()->comment('学年');
            $table->string('year', 5)->nullable()->comment('年度');
            $table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
            $table->string('implementation_no', 100)->nullable()->comment('試験No');
            $table->string('subject_no', 100)->nullable()->comment('教科No');
            $table->string('rating_point')->nullable()->comment('点数・偏差値');
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
        Schema::dropIfExists('student_rating_points');
    }
}
