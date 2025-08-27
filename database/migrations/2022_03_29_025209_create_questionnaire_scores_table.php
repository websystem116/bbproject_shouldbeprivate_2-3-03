<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateQuestionnaireScoresTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("questionnaire_scores", function (Blueprint $table) {
						$table->increments('id');
						$table->integer('user_id')->nullable();
						$table->integer('classroom_score')->nullable();
						$table->integer('subject_score')->nullable();
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
                    Schema::dropIfExists("questionnaire_scores");
                }
            }
        