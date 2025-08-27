<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateSchoolBuildingsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("school_buildings", function (Blueprint $table) {
						$table->increments('id');
						$table->string('name',20)->nullable();
						$table->string('name_short',10)->nullable();
						$table->string(' zipcode',8)->nullable();
						$table->string('address1',30)->nullable();
						$table->string('address2',30)->nullable();
						$table->string('address3',30)->nullable();
						$table->string('tel',15)->nullable();
						$table->string('fax',15)->nullable();
						$table->string('email',50)->nullable();
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
                    Schema::dropIfExists("school_buildings");
                }
            }
        