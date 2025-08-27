@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">業務内容</div>
                <div class="panel-body">


                    <a href="{{ url('/shinzemi/job_description/create') }}" class="btn btn-success btn-sm" title="Add New job_description">
                        新規追加
                    </a>

                    {{-- <form method="GET" action="{{ url("bank") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
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

                    <div>{{ $job_description->total() }} 件中 {{ $job_description->firstItem() }} - {{ $job_description->lastItem() }} 件を表示</div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>業務内容</th>
                                    <th>編集</th>
                                    <th>削除</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($job_description as $item)
                                <tr>

                                    <td>{{ $item->id }} </td>

                                    <td>{{ $item->name }} </td>

                                    <td>
                                        <a href="{{ url('/shinzemi/job_description/' . $item->id . '/edit') }}" title="Edit job_description"><button class="btn btn-primary btn-xs">編集</button></a>
                                    </td>
                                    <td>
                                        <form method="POST" action="job_description/{{ $item->id }}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete job_description" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $job_description->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection