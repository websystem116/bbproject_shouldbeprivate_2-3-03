@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">区分</div>
                <div class="panel-body">


                    <a href="{{ url("classification/create") }}" class="btn btn-success btn-sm" title="Add New classification">
                        新規作成
                    </a>

                    {{-- <form method="GET" action="{{ url("classification") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-info" type="submit">
                                <span>検索</span>
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
                                    <th>項目No</th>
                                    <th>分類コード</th>
                                    <th>項目名</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classification as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->no}} </td>

                                    <td>{{ $item->name}} </td>

                                    <!-- <td><a href="{{ url("/classification/" . $item->id) }}" title="View classification"><button class="btn btn-info btn-xs">View</button></a></td> -->
                                    <td><a href="{{ url("/classification/" . $item->id . "/edit") }}" title="Edit classification"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{route('classification.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field("DELETE") }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $classification->appends(["search" => Request::get("search")])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection