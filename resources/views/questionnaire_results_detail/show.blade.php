
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">questionnaire_results_detail {{ $questionnaire_results_detail->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("questionnaire_results_detail") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("questionnaire_results_detail") ."/". $questionnaire_results_detail->id . "/edit" }}" title="Edit questionnaire_results_detail"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/questionnaire_results_detail/{{ $questionnaire_results_detail->id }}" class="form-horizontal" style="display:inline;">
                                        {{ csrf_field() }}
                                        {{ method_field("delete") }}
                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('Confirm delete')">
                                        Delete
                                        </button>    
                            </form>
                            <br/>
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
										<tr><th>id</th><td>{{$questionnaire_results_detail->id}} </td></tr>
										<tr><th>management_code</th><td>{{$questionnaire_results_detail->management_code}} </td></tr>
										<tr><th>questionnaire_content_id</th><td>{{$questionnaire_results_detail->questionnaire_content_id}} </td></tr>
										<tr><th>school_building_id</th><td>{{$questionnaire_results_detail->school_building_id}} </td></tr>
										<tr><th>school_year_id</th><td>{{$questionnaire_results_detail->school_year_id}} </td></tr>
										<tr><th>alphabet_id_1</th><td>{{$questionnaire_results_detail->alphabet_id_1}} </td></tr>
										<tr><th>subject_id_1</th><td>{{$questionnaire_results_detail->subject_id_1}} </td></tr>
										<tr><th>user_id_1</th><td>{{$questionnaire_results_detail->user_id_1}} </td></tr>
										<tr><th>question_1_1</th><td>{{$questionnaire_results_detail->question_1_1}} </td></tr>
										<tr><th>question_2_1</th><td>{{$questionnaire_results_detail->question_2_1}} </td></tr>
										<tr><th>question_3_1</th><td>{{$questionnaire_results_detail->question_3_1}} </td></tr>
										<tr><th>question_4_1</th><td>{{$questionnaire_results_detail->question_4_1}} </td></tr>
										<tr><th>question_5_1</th><td>{{$questionnaire_results_detail->question_5_1}} </td></tr>
										<tr><th>question_6_1</th><td>{{$questionnaire_results_detail->question_6_1}} </td></tr>
										<tr><th>question_7_1</th><td>{{$questionnaire_results_detail->question_7_1}} </td></tr>
										<tr><th>alphabet_id_2</th><td>{{$questionnaire_results_detail->alphabet_id_2}} </td></tr>
										<tr><th>subject_id_2</th><td>{{$questionnaire_results_detail->subject_id_2}} </td></tr>
										<tr><th>user_id_2</th><td>{{$questionnaire_results_detail->user_id_2}} </td></tr>
										<tr><th>question_1_2</th><td>{{$questionnaire_results_detail->question_1_2}} </td></tr>
										<tr><th>question_2_2</th><td>{{$questionnaire_results_detail->question_2_2}} </td></tr>
										<tr><th>question_3_2</th><td>{{$questionnaire_results_detail->question_3_2}} </td></tr>
										<tr><th>question_4_2</th><td>{{$questionnaire_results_detail->question_4_2}} </td></tr>
										<tr><th>question_5_2</th><td>{{$questionnaire_results_detail->question_5_2}} </td></tr>
										<tr><th>question_6_2</th><td>{{$questionnaire_results_detail->question_6_2}} </td></tr>
										<tr><th>question_7_2</th><td>{{$questionnaire_results_detail->question_7_2}} </td></tr>
										<tr><th>alphabet_id_3</th><td>{{$questionnaire_results_detail->alphabet_id_3}} </td></tr>
										<tr><th>subject_id_3</th><td>{{$questionnaire_results_detail->subject_id_3}} </td></tr>
										<tr><th>user_id_3</th><td>{{$questionnaire_results_detail->user_id_3}} </td></tr>
										<tr><th>question_1_3</th><td>{{$questionnaire_results_detail->question_1_3}} </td></tr>
										<tr><th>question_2_3</th><td>{{$questionnaire_results_detail->question_2_3}} </td></tr>
										<tr><th>question_3_3</th><td>{{$questionnaire_results_detail->question_3_3}} </td></tr>
										<tr><th>question_4_3</th><td>{{$questionnaire_results_detail->question_4_3}} </td></tr>
										<tr><th>question_5_3</th><td>{{$questionnaire_results_detail->question_5_3}} </td></tr>
										<tr><th>question_6_3</th><td>{{$questionnaire_results_detail->question_6_3}} </td></tr>
										<tr><th>question_7_3</th><td>{{$questionnaire_results_detail->question_7_3}} </td></tr>
										<tr><th>alphabet_id_4</th><td>{{$questionnaire_results_detail->alphabet_id_4}} </td></tr>
										<tr><th>subject_id_4</th><td>{{$questionnaire_results_detail->subject_id_4}} </td></tr>
										<tr><th>user_id_4</th><td>{{$questionnaire_results_detail->user_id_4}} </td></tr>
										<tr><th>question_1_4</th><td>{{$questionnaire_results_detail->question_1_4}} </td></tr>
										<tr><th>question_2_4</th><td>{{$questionnaire_results_detail->question_2_4}} </td></tr>
										<tr><th>question_3_4</th><td>{{$questionnaire_results_detail->question_3_4}} </td></tr>
										<tr><th>question_4_4</th><td>{{$questionnaire_results_detail->question_4_4}} </td></tr>
										<tr><th>question_5_4</th><td>{{$questionnaire_results_detail->question_5_4}} </td></tr>
										<tr><th>question_6_4</th><td>{{$questionnaire_results_detail->question_6_4}} </td></tr>
										<tr><th>question_7_4</th><td>{{$questionnaire_results_detail->question_7_4}} </td></tr>
										<tr><th>alphabet_id_5</th><td>{{$questionnaire_results_detail->alphabet_id_5}} </td></tr>
										<tr><th>subject_id_5</th><td>{{$questionnaire_results_detail->subject_id_5}} </td></tr>
										<tr><th>user_id_5</th><td>{{$questionnaire_results_detail->user_id_5}} </td></tr>
										<tr><th>question_1_5</th><td>{{$questionnaire_results_detail->question_1_5}} </td></tr>
										<tr><th>question_2_5</th><td>{{$questionnaire_results_detail->question_2_5}} </td></tr>
										<tr><th>question_3_5</th><td>{{$questionnaire_results_detail->question_3_5}} </td></tr>
										<tr><th>question_4_5</th><td>{{$questionnaire_results_detail->question_4_5}} </td></tr>
										<tr><th>question_5_5</th><td>{{$questionnaire_results_detail->question_5_5}} </td></tr>
										<tr><th>question_6_5</th><td>{{$questionnaire_results_detail->question_6_5}} </td></tr>
										<tr><th>question_7_5</th><td>{{$questionnaire_results_detail->question_7_5}} </td></tr>
										<tr><th>Creator</th><td>{{$questionnaire_results_detail->Creator}} </td></tr>
										<tr><th>Updater</th><td>{{$questionnaire_results_detail->Updater}} </td></tr>
										<tr><th>title</th><td>{{$questionnaire_results_detail->title}} </td></tr>
										<tr><th>summary</th><td>{{$questionnaire_results_detail->summary}} </td></tr>
										<tr><th>question1</th><td>{{$questionnaire_results_detail->question1}} </td></tr>
										<tr><th>question1_compensation</th><td>{{$questionnaire_results_detail->question1_compensation}} </td></tr>
										<tr><th>question1_choice1</th><td>{{$questionnaire_results_detail->question1_choice1}} </td></tr>
										<tr><th>question1_choice2</th><td>{{$questionnaire_results_detail->question1_choice2}} </td></tr>
										<tr><th>question1_choice3</th><td>{{$questionnaire_results_detail->question1_choice3}} </td></tr>
										<tr><th>question1_choice4</th><td>{{$questionnaire_results_detail->question1_choice4}} </td></tr>
										<tr><th>question2</th><td>{{$questionnaire_results_detail->question2}} </td></tr>
										<tr><th>question2_compensation</th><td>{{$questionnaire_results_detail->question2_compensation}} </td></tr>
										<tr><th>question2_choice1</th><td>{{$questionnaire_results_detail->question2_choice1}} </td></tr>
										<tr><th>question2_choice2</th><td>{{$questionnaire_results_detail->question2_choice2}} </td></tr>
										<tr><th>question2_choice3</th><td>{{$questionnaire_results_detail->question2_choice3}} </td></tr>
										<tr><th>question2_choice4</th><td>{{$questionnaire_results_detail->question2_choice4}} </td></tr>
										<tr><th>question3</th><td>{{$questionnaire_results_detail->question3}} </td></tr>
										<tr><th>question3_compensation</th><td>{{$questionnaire_results_detail->question3_compensation}} </td></tr>
										<tr><th>question3_choice1</th><td>{{$questionnaire_results_detail->question3_choice1}} </td></tr>
										<tr><th>question3_choice2</th><td>{{$questionnaire_results_detail->question3_choice2}} </td></tr>
										<tr><th>question3_choice3</th><td>{{$questionnaire_results_detail->question3_choice3}} </td></tr>
										<tr><th>question3_choice4</th><td>{{$questionnaire_results_detail->question3_choice4}} </td></tr>
										<tr><th>question4_compensation</th><td>{{$questionnaire_results_detail->question4_compensation}} </td></tr>
										<tr><th>question4_choice1</th><td>{{$questionnaire_results_detail->question4_choice1}} </td></tr>
										<tr><th>question4_choice2</th><td>{{$questionnaire_results_detail->question4_choice2}} </td></tr>
										<tr><th>question4_choice3</th><td>{{$questionnaire_results_detail->question4_choice3}} </td></tr>
										<tr><th>question4_choice4</th><td>{{$questionnaire_results_detail->question4_choice4}} </td></tr>
										<tr><th>question5</th><td>{{$questionnaire_results_detail->question5}} </td></tr>
										<tr><th>question5_compensation</th><td>{{$questionnaire_results_detail->question5_compensation}} </td></tr>
										<tr><th>question5_choice1</th><td>{{$questionnaire_results_detail->question5_choice1}} </td></tr>
										<tr><th>question5_choice2</th><td>{{$questionnaire_results_detail->question5_choice2}} </td></tr>
										<tr><th>question5_choice3</th><td>{{$questionnaire_results_detail->question5_choice3}} </td></tr>
										<tr><th>question5_choice4</th><td>{{$questionnaire_results_detail->question5_choice4}} </td></tr>
										<tr><th>question6</th><td>{{$questionnaire_results_detail->question6}} </td></tr>
										<tr><th>question6_compensation</th><td>{{$questionnaire_results_detail->question6_compensation}} </td></tr>
										<tr><th>question6_choice1</th><td>{{$questionnaire_results_detail->question6_choice1}} </td></tr>
										<tr><th>question6_choice2</th><td>{{$questionnaire_results_detail->question6_choice2}} </td></tr>
										<tr><th>question6_choice3</th><td>{{$questionnaire_results_detail->question6_choice3}} </td></tr>
										<tr><th>question6_choice4</th><td>{{$questionnaire_results_detail->question6_choice4}} </td></tr>
										<tr><th>question7</th><td>{{$questionnaire_results_detail->question7}} </td></tr>
										<tr><th>question7_compensation</th><td>{{$questionnaire_results_detail->question7_compensation}} </td></tr>
										<tr><th>question7_choice1</th><td>{{$questionnaire_results_detail->question7_choice1}} </td></tr>
										<tr><th>question7_choice2</th><td>{{$questionnaire_results_detail->question7_choice2}} </td></tr>
										<tr><th>question7_choice3</th><td>{{$questionnaire_results_detail->question7_choice3}} </td></tr>
										<tr><th>question7_choice4</th><td>{{$questionnaire_results_detail->question7_choice4}} </td></tr>
										<tr><th>name</th><td>{{$questionnaire_results_detail->name}} </td></tr>
										<tr><th>name_short</th><td>{{$questionnaire_results_detail->name_short}} </td></tr>
										<tr><th> zipcode</th><td>{{$questionnaire_results_detail-> zipcode}} </td></tr>
										<tr><th>address1</th><td>{{$questionnaire_results_detail->address1}} </td></tr>
										<tr><th>address2</th><td>{{$questionnaire_results_detail->address2}} </td></tr>
										<tr><th>address3</th><td>{{$questionnaire_results_detail->address3}} </td></tr>
										<tr><th>tel</th><td>{{$questionnaire_results_detail->tel}} </td></tr>
										<tr><th>fax</th><td>{{$questionnaire_results_detail->fax}} </td></tr>
										<tr><th>email</th><td>{{$questionnaire_results_detail->email}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    