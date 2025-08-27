<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateQuestionnaireResultsDetailsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("questionnaire_results_details", function (Blueprint $table) {
						$table->increments('id');
						$table->integer('management_code')->nullable();
						$table->integer('questionnaire_content_id')->nullable()->unsigned();
						$table->integer('school_building_id')->nullable()->unsigned();
						$table->integer('school_year_id')->nullable();
						$table->integer('alphabet_id_1')->nullable();
						$table->integer('subject_id_1')->nullable();
						$table->integer('user_id_1')->nullable();
						$table->integer('question_1_1')->nullable();
						$table->integer('question_2_1')->nullable();
						$table->integer('question_3_1')->nullable();
						$table->integer('question_4_1')->nullable();
						$table->integer('question_5_1')->nullable();
						$table->integer('question_6_1')->nullable();
						$table->integer('question_7_1')->nullable();
						$table->integer('alphabet_id_2')->nullable();
						$table->integer('subject_id_2')->nullable();
						$table->integer('user_id_2')->nullable();
						$table->integer('question_1_2')->nullable();
						$table->integer('question_2_2')->nullable();
						$table->integer('question_3_2')->nullable();
						$table->integer('question_4_2')->nullable();
						$table->integer('question_5_2')->nullable();
						$table->integer('question_6_2')->nullable();
						$table->integer('question_7_2')->nullable();
						$table->integer('alphabet_id_3')->nullable();
						$table->integer('subject_id_3')->nullable();
						$table->integer('user_id_3')->nullable();
						$table->integer('question_1_3')->nullable();
						$table->integer('question_2_3')->nullable();
						$table->integer('question_3_3')->nullable();
						$table->integer('question_4_3')->nullable();
						$table->integer('question_5_3')->nullable();
						$table->integer('question_6_3')->nullable();
						$table->integer('question_7_3')->nullable();
						$table->integer('alphabet_id_4')->nullable();
						$table->integer('subject_id_4')->nullable();
						$table->integer('user_id_4')->nullable();
						$table->integer('question_1_4')->nullable();
						$table->integer('question_2_4')->nullable();
						$table->integer('question_3_4')->nullable();
						$table->integer('question_4_4')->nullable();
						$table->integer('question_5_4')->nullable();
						$table->integer('question_6_4')->nullable();
						$table->integer('question_7_4')->nullable();
						$table->integer('alphabet_id_5')->nullable();
						$table->integer('subject_id_5')->nullable();
						$table->integer('user_id_5')->nullable();
						$table->integer('question_1_5')->nullable();
						$table->integer('question_2_5')->nullable();
						$table->integer('question_3_5')->nullable();
						$table->integer('question_4_5')->nullable();
						$table->integer('question_5_5')->nullable();
						$table->integer('question_6_5')->nullable();
						$table->integer('question_7_5')->nullable();
						$table->integer('Creator')->nullable();
						$table->integer('Updater')->nullable();
						$table->softDeletes();
						$table->timestamps();
						$table->foreign("questionnaire_content_id")->references("id")->on("questionnaire_contents");
						$table->foreign("school_building_id")->references("id")->on("school_buildings");



						// ----------------------------------------------------
						// -- SELECT [questionnaire_results_details]--
						// ----------------------------------------------------
						// $query = DB::table("questionnaire_results_details")
						// ->leftJoin("questionnaire_contents","questionnaire_contents.id", "=", "questionnaire_results_details.questionnaire_content_id")
						// ->leftJoin("school_buildings","school_buildings.id", "=", "questionnaire_results_details.school_building_id")
						// ->get();
						// dd($query); //For checking



                    });
                }
    
                /**
                 * Reverse the migrations.
                 *
                 * @return void
                 */
                public function down()
                {
                    Schema::dropIfExists("questionnaire_results_details");
                }
            }
        