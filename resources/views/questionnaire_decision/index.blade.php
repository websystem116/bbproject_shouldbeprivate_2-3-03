@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">questionnaire_decision</div>
                <div class="panel-body">


                    <a href="{{ url("questionnaire_decision/create") }}" class="btn btn-success btn-sm" title="Add New questionnaire_decision">
                        Add New
                    </a>

                    <form method="GET" action="{{ url("questionnaire_decision") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
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


                    <!-- <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>questionnaire_contents_id</th>
                                    <th>user_id</th>
                                    <th>classroom_score</th>
                                    <th>subject_score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionnaire_decision as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->questionnaire_contents_id}} </td>

                                    <td>{{ $item->user_id}} </td>

                                    <td>{{ $item->classroom_score}} </td>

                                    <td>{{ $item->subject_score}} </td>

                                    <td><a href="{{ url("/questionnaire_decision/" . $item->id) }}" title="View questionnaire_decision"><button class="btn btn-info btn-xs">View</button></a></td>
                                    <td><a href="{{ url("/questionnaire_decision/" . $item->id . "/edit") }}" title="Edit questionnaire_decision"><button class="btn btn-primary btn-xs">Edit</button></a></td>
                                    <td>
                                        <form method="POST" action="/questionnaire_decision/{{ $item->id }}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper"> {!! $questionnaire_decision->appends(["search" => Request::get("search")])->render() !!} </div> -->
                </div>


            </div>
        </div>
    </div>
</div>
</div>
@endsection