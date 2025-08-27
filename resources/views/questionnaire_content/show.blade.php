@extends("layouts.app")
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">questionnaire_content {{ $questionnaire_content->id }}</div>
                    <div class="panel-body">

                        <a href="{{ url('/shinzemi/questionnaire_content') }}" title="Back"><button
                                class="btn btn-warning btn-xs">Back</button></a>
                        <a href="{{ url('/shinzemi/questionnaire_content') . '/' . $questionnaire_content->id . '/edit' }}"
                            title="Edit questionnaire_content"><button class="btn btn-primary btn-xs">Edit</button></a>
                        <form method="POST" action="/questionnaire_content/{{ $questionnaire_content->id }}"
                            class="form-horizontal" style="display:inline;">
                            {{ csrf_field() }}
                            {{ method_field('delete') }}
                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User"
                                onclick="return confirm('Confirm delete')">
                                Delete
                            </button>
                        </form>
                        <br />
                        <br />
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th>id</th>
                                        <td>{{ $questionnaire_content->id }} </td>
                                    </tr>
                                    <tr>
                                        <th>title</th>
                                        <td>{{ $questionnaire_content->title }} </td>
                                    </tr>
                                    <tr>
                                        <th>summary</th>
                                        <td>{{ $questionnaire_content->summary }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1</th>
                                        <td>{{ $questionnaire_content->question1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1_compensation</th>
                                        <td>{{ $questionnaire_content->question1_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1_choice1</th>
                                        <td>{{ $questionnaire_content->question1_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1_choice2</th>
                                        <td>{{ $questionnaire_content->question1_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1_choice3</th>
                                        <td>{{ $questionnaire_content->question1_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question1_choice4</th>
                                        <td>{{ $questionnaire_content->question1_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2</th>
                                        <td>{{ $questionnaire_content->question2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2_compensation</th>
                                        <td>{{ $questionnaire_content->question2_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2_choice1</th>
                                        <td>{{ $questionnaire_content->question2_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2_choice2</th>
                                        <td>{{ $questionnaire_content->question2_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2_choice3</th>
                                        <td>{{ $questionnaire_content->question2_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question2_choice4</th>
                                        <td>{{ $questionnaire_content->question2_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3</th>
                                        <td>{{ $questionnaire_content->question3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3_compensation</th>
                                        <td>{{ $questionnaire_content->question3_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3_choice1</th>
                                        <td>{{ $questionnaire_content->question3_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3_choice2</th>
                                        <td>{{ $questionnaire_content->question3_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3_choice3</th>
                                        <td>{{ $questionnaire_content->question3_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question3_choice4</th>
                                        <td>{{ $questionnaire_content->question3_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question4_compensation</th>
                                        <td>{{ $questionnaire_content->question4_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question4_choice1</th>
                                        <td>{{ $questionnaire_content->question4_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question4_choice2</th>
                                        <td>{{ $questionnaire_content->question4_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question4_choice3</th>
                                        <td>{{ $questionnaire_content->question4_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question4_choice4</th>
                                        <td>{{ $questionnaire_content->question4_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5</th>
                                        <td>{{ $questionnaire_content->question5 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5_compensation</th>
                                        <td>{{ $questionnaire_content->question5_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5_choice1</th>
                                        <td>{{ $questionnaire_content->question5_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5_choice2</th>
                                        <td>{{ $questionnaire_content->question5_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5_choice3</th>
                                        <td>{{ $questionnaire_content->question5_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question5_choice4</th>
                                        <td>{{ $questionnaire_content->question5_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6</th>
                                        <td>{{ $questionnaire_content->question6 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6_compensation</th>
                                        <td>{{ $questionnaire_content->question6_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6_choice1</th>
                                        <td>{{ $questionnaire_content->question6_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6_choice2</th>
                                        <td>{{ $questionnaire_content->question6_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6_choice3</th>
                                        <td>{{ $questionnaire_content->question6_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question6_choice4</th>
                                        <td>{{ $questionnaire_content->question6_choice4 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7</th>
                                        <td>{{ $questionnaire_content->question7 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7_compensation</th>
                                        <td>{{ $questionnaire_content->question7_compensation }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7_choice1</th>
                                        <td>{{ $questionnaire_content->question7_choice1 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7_choice2</th>
                                        <td>{{ $questionnaire_content->question7_choice2 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7_choice3</th>
                                        <td>{{ $questionnaire_content->question7_choice3 }} </td>
                                    </tr>
                                    <tr>
                                        <th>question7_choice4</th>
                                        <td>{{ $questionnaire_content->question7_choice4 }} </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
