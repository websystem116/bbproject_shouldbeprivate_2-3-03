@extends("layouts.app")
@section("content")
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">銀行マスタ</div>
        <div class="panel-body">


            <div style="display:flex;align-items: center;justify-content: space-between;">

                <div>
                    <a href="{{ url('/shinzemi/bank/create') }}" class="btn btn-success btn-sm" title="Add New bank">
                        新規追加
                    </a>
                </div>

                <div>
                    <form method="GET" action="{{ url('/shinzemi/bank') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">

                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="銀行名" value="{{ request('search') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </span>
                        </div>

                    </form>
                </div>

            </div>


            <br />
            <br />

            <div>{{ $bank->total() }} 件中 {{ $bank->firstItem() }} - {{ $bank->lastItem() }} 件を表示</div>

            <div class="table">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>銀行コード</th>
                            <th>銀行名</th>
                            <th>銀行名（カナ）</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bank as $item)

                        <tr>

                            <td>{{ $item->id}} </td>

                            <td>{{ $item->code}} </td>

                            <td>{{ $item->name}} </td>

                            <td>{{ $item->name_kana}} </td>

                            <td>
                                <a href="{{ url("/bank/" . $item->id . "/edit") }}" title="Edit bank"><button class="btn btn-primary btn-xs">編集</button></a>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('bank.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
                                    {{ csrf_field() }}

                                    {{ method_field("DELETE") }}
                                    <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか。')">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper"> {!! $bank->appends(["search" => Request::get("search")])->render() !!} </div>
            </div>


        </div>
    </div>
</div>
@endsection