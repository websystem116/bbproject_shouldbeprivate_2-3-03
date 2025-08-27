<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_buildings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number')->nullable()->comment('編集用No');
            $table->string('name', 20)->nullable()->comment('校舎名');
            $table->string('name_short', 10)->nullable()->comment('校舎名（略称）');
            $table->string('zipcode', 8)->nullable()->comment('郵便番号');
            $table->string('address1', 30)->nullable()->comment('住所1');
            $table->string('address2', 30)->nullable()->comment('住所2');
            $table->string('address3', 30)->nullable()->comment('住所3');
            $table->string('tel', 15)->nullable()->comment('電話番号');
            $table->string('fax', 15)->nullable()->comment('FAX番号');
            $table->string('email', 50)->nullable()->comment('E-mailアドレス');
            $table->integer('area')->nullable()->default(0)->comment('エリア1:大阪2;奈良');
            $table->integer('order_num')->nullable();
            $table->integer('hidden_flg')->nullable()->default(0);
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
        Schema::dropIfExists('school_buildings');
    }
}
