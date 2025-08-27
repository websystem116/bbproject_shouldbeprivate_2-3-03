
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">authoritie</div>
                            <div class="panel-body">
                            
                            
                                <a href="{{ url("authoritie/create") }}" class="btn btn-success btn-sm" title="Add New authoritie">
                                    Add New
                                </a>

                                <form method="GET" action="{{ url("authoritie") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" placeholder="Search...">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info" type="submit">
                                                <span>Search</span>
                                            </button>
                                        </span>
                                    </div>
                                </form>


                                <br/>
                                <br/>
                                
                                
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr><th>id</th><th>user_id</th><th>password</th><th>classification_code</th><th>item_no</th><th>Is_need_password</th><th>last_login_date</th><th>changed_password_date</th><th>fail_times_login</th></tr>
                                        </thead>
                                        <tbody>
                                        @foreach($authoritie as $item)
                                    
                                    <tr>

                                            <td>{{ $item->id}} </td>

                                            <td>{{ $item->user_id}} </td>

                                            <td>{{ $item->password}} </td>

                                            <td>{{ $item->classification_code}} </td>

                                            <td>{{ $item->item_no}} </td>

                                            <td>{{ $item->Is_need_password}} </td>

                                            <td>{{ $item->last_login_date}} </td>

                                            <td>{{ $item->changed_password_date}} </td>

                                            <td>{{ $item->fail_times_login}} </td>
  
                                                <td><a href="{{ url("/authoritie/" . $item->id) }}" title="View authoritie"><button class="btn btn-info btn-xs">View</button></a></td>
                                                <td><a href="{{ url("/authoritie/" . $item->id . "/edit") }}" title="Edit authoritie"><button class="btn btn-primary btn-xs">Edit</button></a></td>
                                                <td>
                                                    <form method="POST" action="/authoritie/{{ $item->id }}" class="form-horizontal" style="display:inline;">
                                                        {{ csrf_field() }}
                                                        
                                                        {{ method_field("DELETE") }}
                                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('Confirm delete')">
                                                        Delete
                                                        </button>    
                                                    </form>
                                                   </td>
                                              </tr>

                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination-wrapper"> {!! $authoritie->appends(["search" => Request::get("search")])->render() !!} </div>
                                </div>
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    