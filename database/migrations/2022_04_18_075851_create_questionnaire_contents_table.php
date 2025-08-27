<?php
            use Illuminate\Support\Facades\Schema;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Database\Migrations\Migration;
            
            class CreateQuestionnaireContentsTable extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                    Schema::create("questionnaire_contents", function (Blueprint $table) {
						$table->increments('id');
						$table->string('title',30);
						$table->string('summary',200)->nullable();
						$table->string('question1',40)->nullable();
						$table->integer('question1_compensation')->nullable();
						$table->string('question1_choice1',40)->nullable();
						$table->string('question1_choice2',40)->nullable();
						$table->string('question1_choice3',40)->nullable();
						$table->string('question1_choice4',40)->nullable();
						$table->string('question2',40)->nullable();
						$table->integer('question2_compensation')->nullable();
						$table->string('question2_choice1',40)->nullable();
						$table->string('question2_choice2',40)->nullable();
						$table->string('question2_choice3',40)->nullable();
						$table->string('question2_choice4',40)->nullable();
						$table->string('question3',40)->nullable();
						$table->integer('question3_compensation')->nullable();
						$table->string('question3_choice1',40)->nullable();
						$table->string('question3_choice2',40)->nullable();
						$table->string('question3_choice3',40)->nullable();
						$table->string('question3_choice4',40)->nullable();
						$table->integer('question4_compensation')->nullable();
						$table->string('question4_choice1',40)->nullable();
						$table->string('question4_choice2',40)->nullable();
						$table->string('question4_choice3',40)->nullable();
						$table->string('question4_choice4',40)->nullable();
						$table->string('question5',40)->nullable();
						$table->integer('question5_compensation')->nullable();
						$table->string('question5_choice1',40)->nullable();
						$table->string('question5_choice2',40)->nullable();
						$table->string('question5_choice3',40)->nullable();
						$table->string('question5_choice4',40)->nullable();
						$table->string('question6',40)->nullable();
						$table->integer('question6_compensation')->nullable();
						$table->string('question6_choice1',40)->nullable();
						$table->string('question6_choice2',40)->nullable();
						$table->string('question6_choice3',40)->nullable();
						$table->string('question6_choice4',40)->nullable();
						$table->string('question7',40)->nullable();
						$table->integer('question7_compensation')->nullable();
						$table->string('question7_choice1',40)->nullable();
						$table->string('question7_choice2',40)->nullable();
						$table->string('question7_choice3',40)->nullable();
						$table->string('question7_choice4',40)->nullable();
						$table->timestamps();
						$table->softDeletes();



						// ----------------------------------------------------
						// -- SELECT [questionnaire_contents]--
						// ----------------------------------------------------
						// $query = DB::table("questionnaire_contents")
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
                    Schema::dropIfExists("questionnaire_contents");
                }
            }
        