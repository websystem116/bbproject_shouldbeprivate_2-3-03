<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateDiscountsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("discounts", function (Blueprint $table) {
						$table->increments('id');
						$table->string('name',40)->nullable();
						$table->string('name_short',10)->nullable();
						$table->integer('discount_rate_class')->nullable();
						$table->integer('discount_rate_personal')->nullable();
						$table->integer('discount_rate_course')->nullable();
						$table->integer('discount_rate_join')->nullable();
						$table->integer('discount_rate_monthly')->nullable();
						$table->integer('discount_rate_teachingmaterial')->nullable();
						$table->integer('discount_rate_test')->nullable();
						$table->integer('discount_rate_certification')->nullable();
						$table->integer('discount_rate_other')->nullable();
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
                    Schema::dropIfExists("discounts");
                }
            }
        