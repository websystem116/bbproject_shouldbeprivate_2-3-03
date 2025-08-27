<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateBanksTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("banks", function (Blueprint $table) {
						$table->increments('id');
						$table->string('code',4)->nullable();
						$table->string('name',15)->nullable();
						$table->string('name_kana',40)->nullable();
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
                    Schema::dropIfExists("banks");
                }
            }
        