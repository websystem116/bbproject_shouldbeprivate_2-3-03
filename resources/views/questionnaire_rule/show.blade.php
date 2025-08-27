
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">questionnaire_rule {{ $questionnaire_rule->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("questionnaire_rule") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("questionnaire_rule") ."/". $questionnaire_rule->id . "/edit" }}" title="Edit questionnaire_rule"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/questionnaire_rule/{{ $questionnaire_rule->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$questionnaire_rule->id}} </td></tr>
										<tr><th>rankstart</th><td>{{$questionnaire_rule->rankstart}} </td></tr>
										<tr><th>rankend</th><td>{{$questionnaire_rule->rankend}} </td></tr>
										<tr><th>rankscore</th><td>{{$questionnaire_rule->rankscore}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    