@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">銀行支店マスタ</div>
                <div class="panel-body">


                    <div>
                        <a href="{{ route('branch_bank.create') }} " class="btn btn-success btn-sm" title="Add New branch_bank">
                            新規追加
                        </a>
                    </div>


                    <form method="GET" action="{{ url('/shinzemi/branch_bank') }}" accept-charset="UTF-8" class="" role="search" style="display:flex;justify-content: flex-end;">

                        <input type="text" class="" name="name" placeholder="銀行名" value="{{ request('name') }}">
                        <button class="btn btn-info" type="submit">
                            <span>検索</span>
                        </button>

                    </form>

                    <div style="padding-top:24px;">
                        {{ $branch_bank->total() }} 件中 {{ $branch_bank->firstItem() }} - {{ $branch_bank->lastItem() }} 件を表示
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>支店コード</th>
                                    <th>支店銀行コード</th>
                                    <th>銀行名</th>
                                    <th>支店名</th>
                                    <th>支店名（カナ）</th>
                                    {{-- <th>支店郵便番号</th>
                                    <th>支店住所</th>
                                    <th>支店電話番号</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branch_bank as $item)
                                <tr>

                                    <td>{{ $item->id }} </td>

                                    <td>{{ $item->code }} </td>

                                    <td>{{ $item->bank->name }} </td>

                                    <td>{{ $item->name }} </td>

                                    <td>{{ $item->name_kana }} </td>

                                    {{-- <td>{{ $item->zipcode }} </td>

                                    <td>{{ $item->address }} </td>

                                    <td>{{ $item->tel }} </td> --}}

                                    <!-- <td><a href="{{ url('/shinzemi/branch_bank/' . $item->id) }}" title="View branch_bank"><button class="btn btn-info btn-xs">View</button></a></td> -->
                                    <td><a href="{{ url('/shinzemi/branch_bank/' . $item->id . '/edit') }}" title="Edit branch_bank"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{ route('branch_bank.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper">
                            <!-- {!! $branch_bank->appends(['search' => Request::get('search')])->render() !!} -->
                            {!! $branch_bank->appends(request()->all())->links() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection