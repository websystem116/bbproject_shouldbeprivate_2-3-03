<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('password', 20)->nullable();
            $table->integer('classification_code')->nullable();
            $table->string('item_no', 2)->nullable();
            $table->integer('Is_need_password')->nullable();
            $table->dateTime('last_login_date')->nullable();
            $table->dateTime('changed_password_date')->nullable();
            $table->integer('fail_times_login')->nullable();
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
        Schema::dropIfExists('authorities');
    }
}
