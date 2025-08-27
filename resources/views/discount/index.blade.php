@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">割引マスタ</div>
                <div class="panel-body">


                    <a href="{{ url('/shinzemi/discount/create') }}" class="btn btn-success btn-sm" title="Add New discount">
                        新規追加
                    </a>

                    <form method="GET" action="{{ url("discount") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                        <div class="input-group">

                            <input type="text" class="form-control" name="search" placeholder="割引名" value="{{ request("search") }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </span>
                        </div>
                    </form>

                    <br />
                    <br />

                    <div>{{ $discount->total() }} 件中 {{ $discount->firstItem() }} - {{ $discount->lastItem() }} 件を表示</div>

                    <div class="table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>


                                    <th>割引No</th>
                                    <th>割引名</th>
                                    <th>略名</th>

                                    @foreach ($division_codes as $division_code)

                                    <th>
                                        {{ $division_code->name }}
                                    </th>

                                    @endforeach

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($discount as $item)
                                <tr>

                                    <td>{{ $item->id }} </td>

                                    <td>{{ $item->name }} </td>

                                    <td>{{ $item->name_short }} </td>

                                    @foreach ($division_codes as $division_code)
                                    <td>
                                        {{ $item->discountdetails->where('division_code_id', $division_code->id)->first()->discount_rate ?? 0}}％
                                    </td>

                                    @endforeach

                                    <td>
                                        <a href="{{ url('/shinzemi/discount/' . $item->id . '/edit') }}" title="Edit discount">
                                            <button class="btn btn-primary btn-xs">編集</button>
                                        </a>
                                    </td>

                                    <td>
                                        <!-- <form method="POST" action="/discount/{{ $item->id }}" class="form-horizontal" style="display:inline;"> -->
                                        <form method="POST" action="{{ url('/shinzemi/discount' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $discount->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection