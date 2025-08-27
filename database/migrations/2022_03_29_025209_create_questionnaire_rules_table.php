<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateQuestionnaireRulesTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("questionnaire_rules", function (Blueprint $table) {
						$table->increments('id');
						$table->integer('rankstart')->nullable();
						$table->integer('rankend')->nullable();
						$table->integer('rankscore')->nullable();
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
                    Schema::dropIfExists("questionnaire_rules");
                }
            }
        