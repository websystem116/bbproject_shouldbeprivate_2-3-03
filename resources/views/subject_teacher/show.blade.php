
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">subject_teacher {{ $subject_teacher->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("subject_teacher") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("subject_teacher") ."/". $subject_teacher->id . "/edit" }}" title="Edit subject_teacher"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/subject_teacher/{{ $subject_teacher->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$subject_teacher->id}} </td></tr>
										<tr><th>school_year</th><td>{{$subject_teacher->school_year}} </td></tr>
										<tr><th>classification_code_class</th><td>{{$subject_teacher->classification_code_class}} </td></tr>
										<tr><th>item_no_class</th><td>{{$subject_teacher->item_no_class}} </td></tr>
										<tr><th>user_id</th><td>{{$subject_teacher->user_id}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    