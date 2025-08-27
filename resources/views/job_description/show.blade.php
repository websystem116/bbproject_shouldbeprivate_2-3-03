
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">job_description {{ $job_description->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("job_description") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("job_description") ."/". $job_description->id . "/edit" }}" title="Edit job_description"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/job_description/{{ $job_description->id }}" class="form-horizontal" style="display:inline;">
                                        {{ csrf_field() }}
                                        {{ method_field("delete") }}
                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete job_description" onclick="return confirm('Confirm delete')">
                                        Delete
                                        </button>
                            </form>
                            <br/>
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
										<tr><th>id</th><td>{{$job_description->id}} </td></tr>
										<tr><th>code</th><td>{{$job_description->code}} </td></tr>
										<tr><th>name</th><td>{{$job_description->name}} </td></tr>
										<tr><th>name_kana</th><td>{{$job_description->name_kana}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
