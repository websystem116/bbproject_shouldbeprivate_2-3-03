
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">school {{ $school->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("school") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("school") ."/". $school->id . "/edit" }}" title="Edit school"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <!-- <form method="POST" action="/school/{{ $school->id }}" class="form-horizontal" style="display:inline;"> -->
                                <!-- <form method="POST" action="{{route('school.destroy',$school->id)}}" class="form-horizontal" style="display:inline;"> -->
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
										<tr><th>id</th><td>{{$school->id}} </td></tr>
										<tr><th>name</th><td>{{$school->name}} </td></tr>
										<tr><th>name_short</th><td>{{$school->name_short}} </td></tr>
										<tr><th>school_classification</th><td>{{$school->school_classification}} </td></tr>
										<tr><th>university_classification</th><td>{{$school->university_classification}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    