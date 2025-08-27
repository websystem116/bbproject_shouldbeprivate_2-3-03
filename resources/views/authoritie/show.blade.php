
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">authoritie {{ $authoritie->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("authoritie") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("authoritie") ."/". $authoritie->id . "/edit" }}" title="Edit authoritie"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/authoritie/{{ $authoritie->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$authoritie->id}} </td></tr>
										<tr><th>user_id</th><td>{{$authoritie->user_id}} </td></tr>
										<tr><th>password</th><td>{{$authoritie->password}} </td></tr>
										<tr><th>classification_code</th><td>{{$authoritie->classification_code}} </td></tr>
										<tr><th>item_no</th><td>{{$authoritie->item_no}} </td></tr>
										<tr><th>Is_need_password</th><td>{{$authoritie->Is_need_password}} </td></tr>
										<tr><th>last_login_date</th><td>{{$authoritie->last_login_date}} </td></tr>
										<tr><th>changed_password_date</th><td>{{$authoritie->changed_password_date}} </td></tr>
										<tr><th>fail_times_login</th><td>{{$authoritie->fail_times_login}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    