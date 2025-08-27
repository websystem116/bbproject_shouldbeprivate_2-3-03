
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">manage_target {{ $manage_target->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("manage_target") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("manage_target") ."/". $manage_target->id . "/edit" }}" title="Edit manage_target"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/manage_target/{{ $manage_target->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$manage_target->id}} </td></tr>
										<tr><th>year</th><td>{{$manage_target->year}} </td></tr>
										<tr><th>taget_classification</th><td>{{$manage_target->taget_classification}} </td></tr>
										<tr><th>target_value</th><td>{{$manage_target->target_value}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    