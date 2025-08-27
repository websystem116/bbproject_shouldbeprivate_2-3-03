
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">company {{ $company->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("company") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("company") ."/". $company->id . "/edit" }}" title="Edit company"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/company/{{ $company->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$company->id}} </td></tr>
										<tr><th>name</th><td>{{$company->name}} </td></tr>
										<tr><th>name_short</th><td>{{$company->name_short}} </td></tr>
										<tr><th>zipcode</th><td>{{$company->zipcode}} </td></tr>
										<tr><th>address1</th><td>{{$company->address1}} </td></tr>
										<tr><th>address2</th><td>{{$company->address2}} </td></tr>
										<tr><th>address3</th><td>{{$company->address3}} </td></tr>
										<tr><th>tel</th><td>{{$company->tel}} </td></tr>
										<tr><th>fax</th><td>{{$company->fax}} </td></tr>
										<tr><th>email</th><td>{{$company->email}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    