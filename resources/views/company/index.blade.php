@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">会社マスタ</div>
                    <div class="panel-body">

                        {{-- <a href="{{ url('company/create') }}" class="btn btn-success btn-sm" title="Add New company">
                            新規追加
                        </a> --}}



                        <br />
                        <br />

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>名前</th>
                                        <th>略称</th>
                                        <th>郵便番号</th>
                                        <th>住所1</th>
                                        <th>住所2</th>
                                        <th>住所3(建物名)</th>
                                        <th>電話番号</th>
                                        <th>FAX番号</th>
                                        <th>Eメールアドレス</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($company as $item)
                                        <tr>

                                            <td>{{ $item->id }} </td>

                                            <td>{{ $item->name }} </td>

                                            <td>{{ $item->name_short }} </td>

                                            <td>{{ $item->zipcode }} </td>

                                            <td>{{ $item->address1 }} </td>

                                            <td>{{ $item->address2 }} </td>

                                            <td>{{ $item->address3 }} </td>

                                            <td>{{ $item->tel }} </td>

                                            <td>{{ $item->fax }} </td>

                                            <td>{{ $item->email }} </td>
                                            {{-- <td>
                                                <a href="{{ url('/shinzemi/company/' . $item->id) }}" title="View company"><button
                                                        class="btn btn-info btn-xs">詳細</button>
                                                </a>
                                            </td> --}}
                                            <td><a href="{{ url('/shinzemi/company/' . $item->id . '/edit') }}"
                                                    title="Edit company"><button
                                                        class="btn btn-primary btn-xs">編集</button></a></td>

                                            {{-- <td>
                                                <form method="POST" action="/company/{{ $item->id }}"
                                                    class="form-horizontal" style="display:inline;">
                                                    {{ csrf_field() }}

                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Delete User"
                                                        onclick="return confirm('Confirm delete')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td> --}}

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $company->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
