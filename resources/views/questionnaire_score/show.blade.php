
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">questionnaire_score {{ $questionnaire_score->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("questionnaire_score") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("questionnaire_score") ."/". $questionnaire_score->id . "/edit" }}" title="Edit questionnaire_score"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/questionnaire_score/{{ $questionnaire_score->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$questionnaire_score->id}} </td></tr>
										<tr><th>user_id</th><td>{{$questionnaire_score->user_id}} </td></tr>
										<tr><th>classroom_score</th><td>{{$questionnaire_score->classroom_score}} </td></tr>
										<tr><th>subject_score</th><td>{{$questionnaire_score->subject_score}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    