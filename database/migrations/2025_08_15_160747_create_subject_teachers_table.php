<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_year', 2)->nullable()->comment('学年');
            $table->string('classification_code_class', 4)->nullable()->comment('科目コード');
            $table->string('item_no_class', 2)->nullable()->comment('クラスコード');
            $table->integer('user_id')->nullable();
            $table->integer('school_building_id')->nullable()->comment('校舎ID');
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
        Schema::dropIfExists('subject_teachers');
    }
}
