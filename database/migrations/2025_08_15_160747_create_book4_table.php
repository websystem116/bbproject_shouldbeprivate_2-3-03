<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBook4Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book4', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('number')->nullable();
            $table->string('name', 24)->nullable();
            $table->string('name_short', 18)->nullable();
            $table->string('zipcode', 8)->nullable();
            $table->string('address1', 28)->nullable();
            $table->string('address2', 15)->nullable();
            $table->string('address3', 8)->nullable();
            $table->string('tel', 12)->nullable();
            $table->string('fax', 12)->nullable();
            $table->string('email', 8)->nullable();
            $table->integer('area')->nullable();
            $table->string('created_at', 15)->nullable();
            $table->string('updated_at', 16)->nullable();
            $table->string('deleted_at', 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book4');
    }
}
