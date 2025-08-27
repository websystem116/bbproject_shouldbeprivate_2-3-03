
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">branch_bank {{ $branch_bank->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("branch_bank") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("branch_bank") ."/". $branch_bank->id . "/edit" }}" title="Edit branch_bank"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/branch_bank/{{ $branch_bank->id }}" class="form-horizontal" style="display:inline;">
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
										<tr><th>id</th><td>{{$branch_bank->id}} </td></tr>
										<tr><th>code</th><td>{{$branch_bank->code}} </td></tr>
										<tr><th>name</th><td>{{$branch_bank->name}} </td></tr>
										<tr><th>name_kana</th><td>{{$branch_bank->name_kana}} </td></tr>
										<tr><th>zipcode</th><td>{{$branch_bank->zipcode}} </td></tr>
										<tr><th>address</th><td>{{$branch_bank->address}} </td></tr>
										<tr><th>tel</th><td>{{$branch_bank->tel}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    