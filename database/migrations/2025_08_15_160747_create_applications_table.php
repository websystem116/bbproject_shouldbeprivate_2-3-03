<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->dateTime('reqest_date')->nullable()->comment('申込日');
            $table->string('application_no', 128)->nullable()->comment('申込書CD');
            $table->tinyInteger('application_type')->default(0)->comment('申込書タイプ');
            $table->string('description')->nullable()->comment('申込内容');
            $table->longText('detail')->nullable()->comment('申込書詳細');
            $table->tinyInteger('status')->default(0)->comment('ステータス');
            $table->string('created_by', 50)->nullable()->comment('登録者');
            $table->string('charged_by', 50)->nullable()->comment('担当者');
            $table->string('allowed_by', 50)->nullable()->comment('承認者');
            $table->string('sign_filepath')->nullable()->comment('サイン');
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
        Schema::dropIfExists('applications');
    }
}
