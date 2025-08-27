@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">manage_target</div>
                <div class="panel-body">


                    <a href="{{ url("manage_target/create") }}" class="btn btn-success btn-sm" title="Add New manage_target">
                        Add New
                    </a>

                    {{-- <form method="GET" action="{{ url("manage_target") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-info" type="submit">
                                <span>Search</span>
                            </button>
                        </span>
                    </div>
                    </form> --}}


                    <br />
                    <br />


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>year</th>
                                    <th>taget_classification</th>
                                    <th>target_value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($manage_target as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->year}} </td>

                                    <td>{{ $item->taget_classification}} </td>

                                    <td>{{ $item->target_value}} </td>

                                    <td><a href="{{ url("/manage_target/" . $item->id) }}" title="View manage_target"><button class="btn btn-info btn-xs">View</button></a></td>
                                    <td><a href="{{ url("/manage_target/" . $item->id . "/edit") }}" title="Edit manage_target"><button class="btn btn-primary btn-xs">Edit</button></a></td>
                                    <td>
                                        <form method="POST" action="/manage_target/{{ $item->id }}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field("DELETE") }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $manage_target->appends(["search" => Request::get("search")])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection