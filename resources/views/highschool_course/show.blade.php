
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">highschool_course {{ $highschool_course->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("highschool_course") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("highschool_course") ."/". $highschool_course->id . "/edit" }}" title="Edit highschool_course"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/highschool_course/{{ $highschool_course->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$highschool_course->id}} </td></tr>
										<tr><th>school_id</th><td>{{$highschool_course->school_id}} </td></tr>
										<tr><th>name</th><td>{{$highschool_course->name}} </td></tr>
										<tr><th>name_short</th><td>{{$highschool_course->name_short}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    