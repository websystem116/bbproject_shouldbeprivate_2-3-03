<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_comments', function (Blueprint $table) {
            $table->integer('id', true)->comment('請求書説明文マスタ');
            $table->string('comment', 1000)->nullable()->comment('記載内容');
            $table->string('abbreviation', 40)->nullable()->comment('略称');
            $table->integer('division')->default(1)->comment('区分　1:南都WEB 2:りそなNET 3:コンビニ支払い等 4:現金支払い');
            $table->dateTime('created_at')->nullable()->comment('作成日');
            $table->dateTime('updated_at')->nullable()->comment('更新日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_comments');
    }
}
