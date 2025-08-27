<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateSubjectTeachersTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("subject_teachers", function (Blueprint $table) {
						$table->increments('id');
						$table->string('school_year',2)->nullable();
						$table->string('classification_code_class',4)->nullable();
						$table->string('item_no_class',2)->nullable();
						$table->integer('user_id')->nullable();
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
                    Schema::dropIfExists("subject_teachers");
                }
            }
        