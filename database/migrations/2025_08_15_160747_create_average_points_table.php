<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAveragePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('average_points', function (Blueprint $table) {
            $table->increments('id')->comment('平均点No(主キー)');
            $table->string('year', 5)->nullable()->comment('年度');
            $table->string('school_id')->nullable()->comment('学校No');
            $table->string('grade', 5)->nullable()->comment('学年');
            $table->string('result_category_id')->nullable()->comment('成績カテゴリーNo');
            $table->string('implementation_no', 100)->nullable()->comment('実施回');
            $table->string('subject_no')->nullable()->comment('教科No');
            $table->string('average_point')->nullable()->comment('平均点数・偏差値');
            $table->string('created_by')->nullable()->comment('登録者');
            $table->string('updated_by')->nullable()->comment('登録者');
            $table->timestamps();

            $table->index(['school_id', 'grade', 'result_category_id'], 'averagepoints');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('average_points');
    }
}
