<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateSchoolsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("schools", function (Blueprint $table) {
						$table->increments('id');
						$table->string('name',20)->nullable();
						$table->string('name_short',10)->nullable();
						$table->string('school_classification',2)->nullable();
						$table->string('university_classification',2)->nullable();
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
                    Schema::dropIfExists("schools");
                }
            }
        