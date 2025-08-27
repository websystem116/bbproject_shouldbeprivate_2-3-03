<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateHighschoolCoursesTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("highschool_courses", function (Blueprint $table) {
						$table->increments('id');
						$table->string('school_id',4)->nullable();
						$table->string('name',20)->nullable();
						$table->string('name_short',10)->nullable();
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
                    Schema::dropIfExists("highschool_courses");
                }
            }
        