@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">売上区分マスタ</div>
                <div class="panel-body">


                    <a href="{{ url("division_code/create") }}" class="btn btn-success btn-sm" title="Add New division_code">
                        新規追加
                    </a>

                    <form method="GET" action="{{ url("division_code") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="売上区分名" value="{{ request("search") }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
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
                                    <th>名前</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($division_code as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->name}} </td>

                                    <td>
                                        <a href="{{ url("/division_code/" . $item->id . "/edit") }}" title="Edit division_code">
                                            <button class="btn btn-primary btn-xs">編集</button>
                                        </a>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('division_code.destroy', $item->id) }}" accept-charset="UTF-8" style="display:inline">
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
                        <div class="pagination-wrapper"> {!! $division_code->appends(request()->all())->links() !!} </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection