
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">school_building {{ $school_building->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("school_building") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("school_building") ."/". $school_building->id . "/edit" }}" title="Edit school_building"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/school_building/{{ $school_building->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$school_building->id}} </td></tr>
										<tr><th>name</th><td>{{$school_building->name}} </td></tr>
										<tr><th>name_short</th><td>{{$school_building->name_short}} </td></tr>
										<tr><th> zipcode</th><td>{{$school_building-> zipcode}} </td></tr>
										<tr><th>address1</th><td>{{$school_building->address1}} </td></tr>
										<tr><th>address2</th><td>{{$school_building->address2}} </td></tr>
										<tr><th>address3</th><td>{{$school_building->address3}} </td></tr>
										<tr><th>tel</th><td>{{$school_building->tel}} </td></tr>
										<tr><th>fax</th><td>{{$school_building->fax}} </td></tr>
										<tr><th>email</th><td>{{$school_building->email}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    