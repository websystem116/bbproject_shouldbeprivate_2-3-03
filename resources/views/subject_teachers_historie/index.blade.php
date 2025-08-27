@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">subject_teachers_historie</div>
                <div class="panel-body">


                    <a href="{{ url("subject_teachers_historie/create") }}" class="btn btn-success btn-sm" title="Add New subject_teachers_historie">
                        Add New
                    </a>

                    <form method="GET" action="{{ url("subject_teachers_historie") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>Search</span>
                                </button>
                            </span>
                        </div>
                    </form>


                    <br />
                    <br />


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>questionnaire_contents_id</th>
                                    <th>school_year</th>
                                    <th>classification_code_class</th>
                                    <th>item_no_class</th>
                                    <th>user_id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject_teachers_historie as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->questionnaire_contents_id}} </td>

                                    <td>{{ $item->school_year}} </td>

                                    <td>{{ $item->classification_code_class}} </td>

                                    <td>{{ $item->item_no_class}} </td>

                                    <td>{{ $item->user_id}} </td>

                                    <td><a href="{{ url("/subject_teachers_historie/" . $item->id) }}" title="View subject_teachers_historie"><button class="btn btn-info btn-xs">View</button></a></td>
                                    <td><a href="{{ url("/subject_teachers_historie/" . $item->id . "/edit") }}" title="Edit subject_teachers_historie"><button class="btn btn-primary btn-xs">Edit</button></a></td>
                                    <td>
                                        <form method="POST" action="/subject_teachers_historie/{{ $item->id }}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper"> {!! $subject_teachers_historie->appends(["search" => Request::get("search")])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection