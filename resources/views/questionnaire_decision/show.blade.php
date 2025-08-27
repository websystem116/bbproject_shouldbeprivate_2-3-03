
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">questionnaire_decision {{ $questionnaire_decision->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("questionnaire_decision") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("questionnaire_decision") ."/". $questionnaire_decision->id . "/edit" }}" title="Edit questionnaire_decision"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/questionnaire_decision/{{ $questionnaire_decision->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$questionnaire_decision->id}} </td></tr>
										<tr><th>questionnaire_contents_id</th><td>{{$questionnaire_decision->questionnaire_contents_id}} </td></tr>
										<tr><th>user_id</th><td>{{$questionnaire_decision->user_id}} </td></tr>
										<tr><th>classroom_score</th><td>{{$questionnaire_decision->classroom_score}} </td></tr>
										<tr><th>subject_score</th><td>{{$questionnaire_decision->subject_score}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    