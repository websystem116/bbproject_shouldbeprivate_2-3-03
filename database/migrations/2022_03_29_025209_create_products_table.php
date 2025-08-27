<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateProductsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("products", function (Blueprint $table) {
						$table->increments('id');
						$table->string('name',40)->nullable();
						$table->string('name_short',10)->nullable();
						$table->string('description',80)->nullable();
						$table->integer('price')->nullable();
						$table->string('tax_category')->nullable();
						$table->string('division_code',2)->nullable();
						$table->string('item_no',2)->nullable();
						$table->string('tabulation',2)->nullable();
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
                    Schema::dropIfExists("products");
                }
            }
        