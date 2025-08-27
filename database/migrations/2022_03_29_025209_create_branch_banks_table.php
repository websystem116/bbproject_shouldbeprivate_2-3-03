<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateBranchBanksTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("branch_banks", function (Blueprint $table) {
						$table->increments('id');
						$table->string('code',3)->nullable();
						$table->string('name',15)->nullable();
						$table->string('name_kana',40)->nullable();
						$table->string('zipcode',8)->nullable();
						$table->string('address',60)->nullable();
						$table->string('tel',15)->nullable();
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
                    Schema::dropIfExists("branch_banks");
                }
            }
        